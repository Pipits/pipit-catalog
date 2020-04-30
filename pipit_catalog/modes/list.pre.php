<?php

	$ShopAPI = new PerchAPI(1, 'perch_shop');
	$ProductsAPI = new PerchAPI(1, 'perch_shop_products');
    $PerchCategories = new PerchCategories_Categories($API);
	$PerchSets = new PerchCategories_Sets($API);
	$Products   = new PerchShop_Products($ShopAPI);
	$Brands   = new PerchShop_Brands($ShopAPI);

	$Template   = $ShopAPI->get('Template');
	$Template->set('shop/products/product.html', 'shop');

	$per_page = 24;
	$Paging->set_per_page($per_page);
   
	$brands = $Brands->all();

	$URL = $API->app_nav();
	$productsURL = $ProductsAPI->app_nav();
	$productsPATH = $ProductsAPI->app_path();


	$category_groups = [];
	$category_sets = PipitCatalog_Products::get_category_sets();

	foreach($category_sets as $Set) {
		$cats = $PerchCategories->get_for_set($Set->setSlug());
		if(PerchUtil::count($cats)) $category_groups[$Set->setTitle()] = $cats;
	}
	
	
	
	//defaults
	$selected_status = $selected_shipping = $selected_sale = $selected_catID = $selected_brandID = '';
	$sort_id = 'productOrder';
	$sort_type = 'numeric';
	$sort_order = 'ASC';
	$filters = [];
	$listing_opts = ['return-objects' => true,];
	
	
	if (PerchUtil::get('active') && PerchUtil::get('active') != 'all') {
		$selected_status = PerchUtil::get('active');
		$status = (PerchUtil::get('active') == 'true') ? 1 : 0;
		
		$filters[] = [
			'filter' => 'status',
			'match' => 'eq',
			'value' => $status,
		];
	}
	
	
	
	if (PerchUtil::get('sale') && PerchUtil::get('sale') != 'all') {
		$selected_sale = PerchUtil::get('sale');
		$sale = (PerchUtil::get('sale') == 'true') ? 1 : 0;
		
		$filters[] = [
			'filter' => 'on_sale',
			'match' => 'eq',
			'value' => $sale,
		];
	}
	
	
	
	if (PerchUtil::get('shipping') && PerchUtil::get('shipping') != 'all') {
		$selected_shipping = $_GET['shipping'];
		$shipping = (PerchUtil::get('shipping') == 'true') ? 1 : 0;

		$filters[] = [
			'filter' => 'requires_shipping',
			'match' => 'eq',
			'value' => $shipping,
		];
	}
	
	

	if (PerchUtil::get('brand')) {
		$selected_brandID = PerchUtil::get('brand');
		$filters[] = [
			'filter' => 'brand',
			'match' => 'eq',
			'value' => $selected_brandID,
		];
	}


	
	if (PerchUtil::get('sort')) {
		$sort_id = PerchUtil::get('sort');
		$sort_order = 'DESC';
		$sort_type = 'alpha';
		$numerical_sorts = ['price', 'stock', 'status'];

		if(substr($sort_id, 0, 1) === '^') {
			$sort_id = substr($sort_id, 1);
			$sort_order = 'ASC';
		}


		if(in_array($sort_id, $numerical_sorts)) {
			$sort_type = 'numeric';
		}
	}
	
	$listing_opts['sort'] = $sort_id;
	$listing_opts['sort-order'] = $sort_order;
	$listing_opts['sort-type'] = $sort_type;
	
	

	if (PerchUtil::get('q')) {
		$term = PerchUtil::get('sort');
	    $filters[] = [
			'filter' => 'title',
			'match' => 'contains',
			'value' => $term,
		];
	}
	
	

	if (PerchUtil::get('category')) {
		$cat = $PerchCategories->find(PerchUtil::get('category'), true);
		$selected_catID = html_entity_decode($cat->catID());
		$listing_opts['category'] = html_entity_decode($cat->catPath());
	}
	
	
	
	$listing_opts['filter'] = $filters;
	if(count($filters) > 1) $listing_opts['filter-mode'] = 'ungrouped';
	
 
	
	$where_callback = function (PerchQuery $Query) {
    	$Query->where[] =  'productDeleted IS NULL';
    	$Query->where[] = 'parentID IS NULL';
        return $Query;
	};



	$products_for_paging = $Products->get_filtered_listing($listing_opts, $where_callback);
	if($products_for_paging) {
		$listing_opts['paginate'] = true;
		$listing_opts['count'] = $per_page;
		
		$products = $Products->get_filtered_listing($listing_opts, $where_callback, true);
		$Paging->set_total(count($products_for_paging));
	} else {
		$search_message = $HTML->warning_message('No matching products found.');
	}