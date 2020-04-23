<?php
echo '<form method="get" action="'.$API->app_path().'" class="app form-simple pipit-filters">';

	if(!$Settings->get('pipit_catalog_hideCat')->val() && PerchUtil::count($category_groups)) {
		$group_opts = [];
		$group_opts[''] = [ ['value' => '', 'label' => ''] ];

		foreach($category_groups as $key => $categories) {
			$group_opts[$key] = [];

			foreach($categories as $Category) {
				$group_opts[$key][] = [
					'value' => $Category->catID(),
					'label' => $Category->catDisplayPath(),
				];
			}
		}
		
		echo $Form->grouped_select_field('category', 'Categories', $group_opts, $selected_catID, 'pipit-filters__choices');
	}
	
	


	if(!$Settings->get('pipit_catalog_hideBrand')->val() && PerchUtil::count($brands)) {
		$brand_opts = [];
		$brand_opts[] = ['value' => '', 'label' => ''];
		foreach($brands as $brand) {
			$brand_opts[] = [
				'value' => $brand->brandID(),
				'label' => $brand->brandTitle(),
			];
		}
		
		echo $Form->select_field('brand', 'Brands', $brand_opts, $selected_brandID, 'pipit-filters__choices');
	}
	



	if(!$Settings->get('pipit_catalog_hideStatus')->val()) {
		$status_opts = [
			['value' => '', 'label' => ''],
			['value' => 'true', 'label' => 'Active'],
			['value' => 'false', 'label' => 'Inactive'],
		];

		echo $Form->select_field('active', 'Status', $status_opts, $selected_status, 'pipit-filters__choices');
	}

	


	if(!$Settings->get('pipit_catalog_hideSale')->val()) {
		$sale_opts = [
			['value' => '', 'label' => ''],
			['value' => 'true', 'label' => 'Yes'],
			['value' => 'false', 'label' => 'No'],
		];
		
		echo $Form->select_field('sale', 'On Sale', $sale_opts, $selected_sale, 'pipit-filters__choices');
	}
		



	if(!$Settings->get('pipit_catalog_hideShipping')->val()) {
		$shipping_opts = [
			['value' => '', 'label' => ''],
			['value' => 'true', 'label' => 'Required'],
			['value' => 'false', 'label' => 'No Shipping'],
		];
		
		echo $Form->select_field('shipping', 'Shipping', $shipping_opts, $selected_shipping, 'pipit-filters__choices');
	}




	echo '<div class="submit-bar-actions"><input type="submit" id="btnSubmit" value="Filter" class="button button-simple"></div>';
echo $Form->form_end();