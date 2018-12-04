<?php
    include(__DIR__.'/../../../core/inc/api.php');
	
	
	$API  = new PerchAPI(1.0, 'pipit_catalog');
	$HTML = $API->get('HTML');
	$Settings = $API->get('Settings');
    
    
    $ShopAPI    = new PerchAPI(1, 'perch_shop');
    $Products   = new PerchShop_Products($ShopAPI);
    $Settings = $API->get('Settings');
    $product_url = false;

    // find product
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $Product = $Products->find($id);

        $product_url = $Settings->get('perch_shop_product_url')->val();
        $product_url = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', '_replace_vars', $product_url);
        

        $view_page_url = rtrim($Settings->get('siteURL')->val(), '/').$product_url;
        
        // inactive
        if($Product->status() == '1') {
            PerchSystem::redirect($view_page_url);
        }
    }


    function _replace_vars($matches)
    {
        global $Product;

        if($matches) {
            $value = $matches[1];
            return $Product->$value();
        }
        
    }

    include(PERCH_CORE . '/inc/top.php');
    if($Product->status() != '1') {
        echo $HTML->warning_block('Product Inactive', 'The product status is set to inactive. Inactive products cannot be viewed on the site.');
    } else {
        echo $HTML->warning_block('Opps', 'The product URL is not set, or the product could not be found.');
    }
    include(PERCH_CORE . '/inc/btm.php');