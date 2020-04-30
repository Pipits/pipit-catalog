<?php

$items = $Form->find_items('p-');
$ordered_products = [];

if (PerchUtil::count($items)) {

    foreach ($items as $productID => $productOrder) {

        $Product = $Products->find($productID);
        if (is_object($Product)) {
            $ordered_products[] = [
                'id' => $productID,
                'order' => $productOrder
            ];
        }

    }


    
    file_put_contents(PipitCatalog_Products::get_resource_dir() . '/brand_' . $Brand->id() . '.json', json_encode($ordered_products));

    PerchUtil::redirect($API->app_path() .'/reorder/?context=brand&success=1&brandID=' . $Brand->id());
}
