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


    //
    PerchUtil::debug($ordered_products);
    file_put_contents(PipitCatalog_Products::get_resource_dir() . '/cat_' . $Category->id() . '.json', json_encode($ordered_products));

    PerchUtil::redirect($API->app_path() .'/reorder/?context=cat&success=1&catID=' . $Category->id());
}
