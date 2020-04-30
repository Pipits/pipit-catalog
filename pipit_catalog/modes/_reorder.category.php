<?php

echo '<div class="inner">';

if (PerchUtil::count($products)) {
    echo $Form->form_start('reorder', 'reorder');

    echo '<ol class="reorder_list basic-sortable sortable-tree">';

    foreach ($products as $key => $Product) {
        echo '<li><div>';
        echo '<input type="text" name="p-' . $Product->productID() . '" value="' . (1 + $key) . '" />';

        if (!$Settings->get('pipit_catalog_hideProductImages')->val()) {
            $no_img = '<img class="listing__thumb" src="' . $API->app_path() . '/assets/images/no-image.png' . '" alt="Preview: no image" />';
            $dynamic_fields = PerchUtil::json_safe_decode($Product->productDynamicFields(), true);

            $Tag = $Template->find_tag('image');
            if (!$Tag || !isset($dynamic_fields['image'])) {
                echo $no_img;
            } else {
                $FieldType = PerchFieldTypes::get('image', false, $Tag);
                echo $FieldType->render_admin_listing($dynamic_fields['image']);
            }
        } else {
            echo PerchUI::icon('ext/o-shirt');
        }

        echo $HTML->encode($Product->title()) . '</div>';
        echo '</li>';
    }

    echo '</ol>';

    echo $Form->hidden('orders', '');
    echo $Form->hidden('category', $Category->catID());
    echo $Form->submit_field('reorder', 'Save Changes', $API->app_path() . '/reorder/?context=cat');

    echo $Form->form_end();
} else {
    echo $HTML->warning_message('No products found in this category.');
}

echo '</div>';