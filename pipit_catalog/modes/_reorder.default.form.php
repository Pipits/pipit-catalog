<?php

$items = $Form->find_items('p-');

if (PerchUtil::count($items)) {

    foreach ($items as $productID => $productOrder) {

        $Product = $Products->find($productID);
        if (is_object($Product)) {
            $data = array('productOrder' => $productOrder);
            $Product->update($data);
            $Product->index($Template);
        }

    }

    $Alert->set('success', $Lang->get('Product orders successfully updated.'));
    PerchUtil::redirect($API->app_path());
}
