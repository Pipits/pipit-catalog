<?php
if (!isset($smartbar_selection)) $smartbar_selection = 'products';

$ParentSmartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
	
	$ParentSmartbar->add_item([
        'active' => $smartbar_selection=='products',
        'title' => $Lang->get('Products'),
        'link'  => $API->app_nav(),
        'icon'  => 'ext/o-shirt',
    ]);
	


	if($smartbar_selection == 'products') {
		if(!$Settings->get('pipit_catalog_hideSearch')->val()) {
			$ParentSmartbar->add_item([
				'active' => false,
				'type'   => 'search',
				'title'  => 'Search',
				'arg'    => 'q',
				'icon'   => 'core/search',
				'position' => 'end',
			]);
		}
	
		$ParentSmartbar->add_item([
			'title' => $Lang->get('Clear filters'),
			'link'  => $API->app_nav(),
			'icon'    => 'core/circle-delete',
			'position' => 'end',
		]);
	}

	

	if($smartbar_selection == 'reorder') {
		$ParentSmartbar->add_item([
			'active' => true,
            'type' => 'toggle',
            'arg'    => 'context',
            'options' => [
                [
                    'title' => 'All',
                    'value' => '',
                    'icon' => '',
                ],
                [
                    'title' => 'Category',
                    'value' => 'cat',
                    'icon' => ''
				],
				[
                    'title' => 'Brand',
                    'value' => 'brand',
                    'icon' => ''
				],
            ]
		]);
		


		
		switch($view) {
			case 'cat_detail':
				include(__DIR__.'/_reorder.category.smartbar.php');
			break;

			case 'brand_detail':
				include(__DIR__.'/_reorder.brand.smartbar.php');
			break;
		}
	}


	
	

	$ParentSmartbar->add_item([
		'active' => $smartbar_selection=='reorder',
        'title' => $Lang->get('Reorder'),
        'link'  => $API->app_nav().'/reorder/',
        'icon'  => 'core/menu',
		'position' => 'end',
		'priv' => 'pipit_catalog.reorder'
	]);
	
	
	$ParentSmartbar->add_item([
		'active' => $smartbar_selection=='republish',
        'title' => $Lang->get('Republish'),
        'link'  => $API->app_nav().'/republish/',
        'icon'  => 'core/documents',
		'position' => 'end',
		'priv' => 'pipit_catalog.republish'
    ]);

echo $ParentSmartbar->render();