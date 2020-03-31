<?php
if (!isset($smartbar_selection)) $smartbar_selection = 'products';

$ParentSmartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
	
	$ParentSmartbar->add_item([
        'active' => $smartbar_selection=='products',
        'title' => $Lang->get('Products'),
        'link'  => $URL,
        'icon'  => 'ext/o-shirt',
    ]);
	
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
		'link'  => $URL,
		'icon'    => 'core/circle-delete',
		'position' => 'end',
	]);

	$ParentSmartbar->add_item([
        'title' => $Lang->get('Reorder'),
        'link'  => $API->app_nav().'/reorder/',
        'icon'  => 'core/menu',
        'position' => 'end',
    ]);

echo $ParentSmartbar->render();