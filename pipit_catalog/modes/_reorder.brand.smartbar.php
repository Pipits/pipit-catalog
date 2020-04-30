<?php

$ParentSmartbar->add_item([
    'active' => true,
    'type'  => 'breadcrumb',
    'links' => [
        [
            'title' => 'Brands',
            'link' => $API->app_nav() . '/reorder/?context=brand',
        ],
        [
            'title' => $Brand->brandTitle(),
            'link' => $API->app_nav() . '/reorder/?context=brand&brandID=' . $Brand->id(),
            'translate' => false,
        ],
    ]
]);
