<?php

$links[] = [
    'title' => 'Categories',
    'link' => $API->app_nav() . '/reorder/?context=cat',
];

foreach (explode('›', $Category->catDisplayPath()) as $part) {
    $categories = $PerchCategories->get_filtered_listing([
        'template' => 'categories/category.html',
        'skip-template' => true,
        'count' => 1,
        'filter' => [
            [
                'filter' => 'setID',
                'value' => $Category->setID()
            ],
            [
                'filter' => 'catTitle',
                'value' => trim($part)
            ],
        ],
    ]);


    $categories = $PerchCategories->return_instances($categories);


    if (PerchUtil::count($categories)) {
        $links[] = [
            'title' => trim($part),
            'link' => $API->app_nav() . '/reorder/?context=cat&catID=' . $categories[0]->id(),
            'translate' => false,
        ];
    }
}

$ParentSmartbar->add_item([
    'active' => true,
    'type'  => 'breadcrumb',
    'links' => $links
]);
