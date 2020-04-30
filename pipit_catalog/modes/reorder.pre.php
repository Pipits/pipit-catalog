<?php
    $ShopAPI            = new PerchAPI(1, 'perch_shop');
    $ProductsAPI        = new PerchAPI(1, 'perch_shop_products');
    $Products           = new PerchShop_Products($ShopAPI);
    $Brands             = new PerchShop_Brands($ShopAPI);
    
    $CatalogProducts    = new PipitCatalog_Products;
    $PerchCategories    = new PerchCategories_Categories($API);
    $PerchSets          = new PerchCategories_Sets($API);

    $Settings           = $API->get('Settings');
    $Template           = $ShopAPI->get('Template');
    
    $Template->set('shop/products/product.html', 'shop');
    $smartbar_selection = 'reorder';





    $Form = $API->get('Form');

    if ($Form->submitted()) 
    {
        $for = 'all';

        if (isset($Form->orig_post['category'])) {
            $Category = $PerchCategories->find( (int) $Form->orig_post['category'] );
            if(is_object($Category)) {
                $for = 'category';
            } else {
                PerchSystem::redirect($API->app_path() . '/reorder/?context=cat');
            }
        }



        if (isset($Form->orig_post['brand'])) {
            $Brand = $Brands->find( (int) $Form->orig_post['brand'] );
            if(is_object($Brand)) {
                $for = 'brand';
            } else {
                PerchSystem::redirect($API->app_path() . '/reorder/?context=brand');
            }
        }



        switch($for) {
            case 'category':
                include(__DIR__ .'/_reorder.category.form.php');
            break;

            case 'brand':
                include(__DIR__ .'/_reorder.brand.form.php');
            break;

            default:
                include(__DIR__ .'/_reorder.default.form.php');
        }   		
    }
    




    $sort_id = 'productOrder';
	$sort_order = 'ASC';
	$filters = [];
	$listing_opts = [
        'return-objects' => true,
        'sort' => 'productOrder',
        'sort-type' => 'numeric',
        'sort-order' => 'ASC',
    ];

    $where_callback = function (PerchQuery $Query)
    {
    	$Query->where[] =  'productDeleted IS NULL';
    	$Query->where[] = 'parentID IS NULL';
        return $Query;
	};
    




    $view = 'all';

    if(PerchUtil::get('context') == 'cat') {
        $view = (PerchUtil::get('catID')) ? 'cat_detail' : 'cat_list';
    } elseif(PerchUtil::get('context') == 'brand') {
        $view = (PerchUtil::get('brandID')) ? 'brand_detail' : 'brand_list';
    }


    switch($view) {
        case 'cat_list':
            $category_sets = PipitCatalog_Products::get_category_sets();

            foreach($category_sets as $Set) {
                $cats = $PerchCategories->get_for_set($Set->setSlug());
                if(PerchUtil::count($cats)) $category_groups[$Set->setTitle()] = $cats;
            }
        break;


        case 'cat_detail':
            $catID = (int) PerchUtil::get('catID');
            $Category = $PerchCategories->get_one_by('catID', $catID);
            if(!is_object($Category)) PerchSystem::redirect($API->app_path() . '/reorder/?context=cat');
            $products = $CatalogProducts->get_ordered_products_for('category', $Category->id(), [], false);
        break;



        case 'brand_list':
            $brands = $Brands->all();
        break;


        case 'brand_detail':
            $Brand = $Brands->find( (int) PerchUtil::get('brandID') );
            if(!is_object($Brand)) PerchSystem::redirect($API->app_path() . '/reorder/?context=brand');
            $products = $CatalogProducts->get_ordered_products_for('brand', $Brand->id(), [], false);
        break;



        default:
            $products = $Products->get_filtered_listing($listing_opts, $where_callback, true);
    }