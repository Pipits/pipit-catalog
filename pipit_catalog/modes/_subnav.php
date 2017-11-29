<?php

	PerchUI::set_subnav([
        [
            'page' => [
				'pipit_catalog',
			],
            'label'=> 'Catalog',
        ], 
		[
            'page' => [
				'perch_shop_products',
			],
            'label'=> 'Products'
        ],
		[
            'page' => [
				'perch_shop_products/brands',
			],
            'label'=> 'Brands'
        ],
		[
            'page' => [
				'perch_shop_products/options',
			],
            'label'=> 'Options'
        ], 
    ], $CurrentUser);

	