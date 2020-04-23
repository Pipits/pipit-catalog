<?php

class PipitCatalog_Util {

    /**
     * Get product category sets from product.html
     * @return array
     */
    static function get_product_category_sets() {
        $API = new PerchAPI(1.0, 'perch_shop');
        $Template = $API->get('Template');
        $Template->set('shop/products/product.html', 'shop');

        $tags = $Template->find_all_tags('categories');
        $sets = [];
        
        if(PerchUtil::count($tags)) {
            foreach($tags as $Tag) {
                if($Tag->is_set('set')) $sets[] = $Tag->set();
            }
        }

        
        return array_values( array_unique($sets) );
    }
}