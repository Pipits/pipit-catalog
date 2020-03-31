<?php
	$ShopAPI    = new PerchAPI(1, 'perch_shop');
    $ProductsAPI    = new PerchAPI(1, 'perch_shop_products');
    $Products   = new PerchShop_Products($ShopAPI);
    $Settings = $API->get('Settings');
    

    $Template   = $ShopAPI->get('Template');
    $Template->set('shop/products/product.html', 'shop');

    $Form = $API->get('Form');

    if ($Form->submitted()) 
    {
   		$items = $Form->find_items('p-');
        if (PerchUtil::count($items)) 
        {
            foreach($items as $productID=>$productOrder) 
            {
                $Product = $Products->find($productID);
                if (is_object($Product)) 
                {
                    $data = array('productOrder'=>$productOrder);
                    $Product->update($data);
                    $Product->index($Template);
                }
            }

   			$Alert->set('success', $Lang->get('Product orders successfully updated.'));
            PerchUtil::redirect($API->app_path());
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
    
    $products = $Products->get_filtered_listing($listing_opts, $where_callback, true);