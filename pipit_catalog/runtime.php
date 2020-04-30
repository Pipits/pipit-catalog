<?php
    include_once('lib/PipitCatalog_Products.class.php');


    /**
     * 
     */
    function pipit_catalog_products($for='', $id='', $opts=[], $return=false) {
        $Products = new PipitCatalog_Products;
        $types = ['category', 'brand'];
        
        if(!$for || !in_array($for, $types)) {
            PerchUtil::debug('No matching type given. Using perch_shop_products()', 'notice');
            return perch_shop_products($opts, $return);
        }

        $products = $Products->get_ordered_products_for($for, $id, $opts, true, false);
        if( $return || (isset($opts['skip-template']) && $opts['skip-template']) ) return $products;
        echo $products['html'];
    }