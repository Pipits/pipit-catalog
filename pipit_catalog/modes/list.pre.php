<?php

    $HTML = $API->get('HTML');
	$Settings = $API->get('Settings');
	$Form = $API->get('Form');
	
	$per_page = 24;
	$Paging = $API->get('Paging');
	$Paging->set_per_page($per_page);
	
	$PerchAPI    = new PerchAPI(1.0, 'core');
    $PerchCategories = new PerchCategories_Categories($PerchAPI);
	$PerchSets = new PerchCategories_Sets($PerchAPI);
   
	$ShopAPI    = new PerchAPI(1, 'perch_shop');
	$ProductsAPI    = new PerchAPI(1, 'perch_shop_products');


	$Products   = new PerchShop_Products($ShopAPI);
	$Brands   = new PerchShop_Brands($ShopAPI);
	$brands = $Brands->all();
	
	$URL = $API->app_nav();
	$productsURL = $ProductsAPI->app_nav();
	$productsPATH = $ProductsAPI->app_path();
	
	if($Settings->get('pipit_catalog_productsSet')->val())
	{
		$setID = $Settings->get('pipit_catalog_productsSet')->val();
		$Set = $PerchSets->find($setID, true);
		$setSlug = $Set->setSlug();
		
		$Categories = $PerchCategories->get_for_set($setSlug);
	}
	else
	{
		$message = $HTML->warning_message('Add the Products Set in the %ssettings%s to enable Category filtering', '<a class="go progress-link" href="'.PERCH_LOGINPATH.'/core/settings/#pipit_catalog">', '</a>');
	}
	
	
	
	//defaults
	$selected_status = $selected_shipping = $selected_sale = $selected_catID = $selected_brandID = '';
	$sort_id = 'title';
	$sort_order = 'ASC';
	$filters = [];
	$listing_opts = ['return-objects' => true,];
	
	
	if (isset($_GET['active']) && $_GET['active'] != '' && $_GET['active'] != 'all') 
	{
		$selected_status = $_GET['active'];
		
		if($_GET['active'] == 'true')
		{
			$status = 1;
		}
		else
		{
			$status = 0;
		}
		
		$filters[] = [
				'filter' => 'status',
				'match' => 'eq',
				'value' => $status,
			];
	}
	
	
	
	if (isset($_GET['sale']) && $_GET['sale'] != '' && $_GET['sale'] != 'all') 
	{
		$selected_sale = $_GET['sale'];
		
		if($_GET['sale'] == 'true')
		{
			$sale = 1;
		}
		else
		{
			$sale = 0;
		}
		
		$filters[] = [
				'filter' => 'on_sale',
				'match' => 'eq',
				'value' => $sale,
			];
	}
	
	
	
	if (isset($_GET['shipping']) && $_GET['shipping'] != '' && $_GET['shipping'] != 'all') 
	{
		$selected_shipping = $_GET['shipping'];
		
		if($_GET['shipping'] == 'true')
		{
			$shipping = 1;
		}
		else
		{
			$shipping = 0;
		}
		
		$filters[] = [
				'filter' => 'requires_shipping',
				'match' => 'eq',
				'value' => $shipping,
			];
	}
	
	
	if (isset($_GET['brand']) && $_GET['brand'] != '') 
	{
		$selected_brandID = $_GET['brand'];
		$filters[] = [
				'filter' => 'brand',
				'match' => 'eq',
				'value' => $selected_brandID,
			];
	}

	if (isset($_GET['sort']) && $_GET['sort'] != '') 
	{
		if(substr($_GET['sort'], 0, 1) === '^')
		{
			$sort_id = substr($_GET['sort'], 1);
			$sort_order = 'ASC';
		}
		else
		{
			$sort_id = $_GET['sort'];
			$sort_order = 'DESC';
		}

		if($sort_id === 'price' || $sort_id === 'stock')
		{
			$listing_opts['sort-type'] = 'numeric';	
		}
	}
	
	$listing_opts['sort'] = $sort_id;
	$listing_opts['sort-order'] = $sort_order;
	
	
	if (isset($_GET['q']) && $_GET['q']!='') 
	{
	    $term = $_GET['q'];
	    $filters[] = [
				'filter' => 'title',
				'match' => 'contains',
				'value' => $term,
			];
	}
	
	
	if (isset($_GET['category']) && $_GET['category'] != '') 
	{
		$cat = $PerchCategories->find($_GET['category'], true);
		$selected_catID = html_entity_decode($cat->catID());
		$listing_opts['category'] = html_entity_decode($cat->catPath());
	}
	
	
	
	$listing_opts['filter'] = $filters;
	if(count($filters) > 1)
	{
		$listing_opts['filter-mode'] = 'ungrouped';
	}
 
	
	$where_callback = function (PerchQuery $Query)
    {
    	$Query->where[] =  'productDeleted IS NULL';
    	$Query->where[] = 'parentID IS NULL';
        return $Query;
	};

	$products_for_paging = $Products->get_filtered_listing($listing_opts, $where_callback);
	if($products_for_paging)
	{
		$listing_opts['paginate'] = true;
		$listing_opts['count'] = $per_page;
		
		
		$products = $Products->get_filtered_listing($listing_opts, $where_callback, true);
		$Paging->set_total(count($products_for_paging));
	}
	else
	{
		$search_message = $HTML->warning_message('No matching products found.');
	}