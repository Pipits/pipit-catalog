<?php
	$ShopAPI    = new PerchAPI(1, 'perch_shop');
    $ProductsAPI    = new PerchAPI(1, 'perch_shop_products');
    $Products   = new PerchShop_Products($ShopAPI);
    $Template   = $ShopAPI->get('Template');
    $Template->set('shop/products/product.html', 'shop');
    
    $smartbar_selection = 'republish';
    $sort_id = 'productOrder';
	$sort_order = 'ASC';
	$listing_opts = ['return-objects' => true,];

    $where_callback = function (PerchQuery $Query) {
    	$Query->where[] =  'productDeleted IS NULL';
    	$Query->where[] = 'parentID IS NULL';
        return $Query;
    };
    


    
    $Form = $API->get('Form');
    if ($Form->submitted()) {
        $products = $Products->get_filtered_listing($listing_opts, $where_callback, true);

        if (PerchUtil::count($products)) {
            foreach($products as $Product) {
                if (is_object($Product)) $Product->index($Template);
            }

   			$Alert->set('success', $Lang->get('Products successfully republished. Return to %s Product List %s', '<a href="'. $API->app_path() .'">', '</a>'));
   		}
    }