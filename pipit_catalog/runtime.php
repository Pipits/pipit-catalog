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








    /**
     * @param string $slug
     * @return array
     */
    function pipit_catalog_get_variants($slug) {
        $products = perch_shop_product_variants($slug, [
            'skip-template' => true,
            'api' => true,
        ]);
    
    
        $keys = [
            'productID',
            'parentID',
            'slug',
            'sku',
            'productVariantDesc',
    
            'price',
            'sale_price',	
            'trade_price',	
            'on_sale',	
            
            
            'stock_status',
            'stock_level',
            'stock_location',
            
            // 'has_variants',
        ];
    
    
    
        $opt_keys = [
            'optionID',
            'optionTitle',
            'options',
            // 'productvalues',
        ];


        $options = [];

        if(isset($products[0]['options'])) {
            $options = $products[0]['options'];
        }

        $options = array_map(function(&$item) use($opt_keys) {
            $out = [];
    
            foreach($opt_keys as $key) {
                $out[$key] = (isset($item[$key])) ? $item[$key] : false;
            }
            
            return $out;
        }, $options);
    



        $products = array_map(function(&$item) use($keys) {
            $out = [];
    
            foreach($keys as $key) {
                $out[$key] = (isset($item[$key])) ? $item[$key] : false;
            }
            
            return $out;
        }, $products);




    
        return ['products' => $products, 'options' => $options];
    }