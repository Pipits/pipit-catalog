<?php
    # Main panel
	echo $HTML->title_panel([
    	'heading' => $Lang->get('Product Catalog'),
    	'button'  => [
			'text' => $Lang->get('Add a Product'),
			'link' => $productsURL.'/product/edit/',
			'icon' => 'core/plus',
			'priv' => 'perch_shop.products.create',
		],
    ], $CurrentUser);

    if (isset($message)) echo $message;
	

	$persist_args = '';
	include(__DIR__.'/list.smartbar.php');
	include(__DIR__.'/list.filters_smartbar.php');
	
	
	

	
	$Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
		
		if(!$Settings->get('pipit_catalog_hideProductImages')->val()) {
			$Listing->add_col([
				'title'     => 'Image',
				'value'     => function($Item) use($API, $Template) {
					$no_img = '<img class="listing__thumb" src="'.$API->app_path().'/assets/images/no-image.png'.'" alt="Preview: no image" />';
					$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);

					$Tag = $Template->find_tag('image');
					if(!$Tag || !isset($dynamic_fields['image'])) return $no_img;

					$FieldType = PerchFieldTypes::get('image', false, $Tag);
					return $FieldType->render_admin_listing($dynamic_fields['image']);
				},
			]);
		}
		


		$Listing->add_col([
			'title'     => 'SKU',
			'value'     => 'sku',
			'sort'      => 'sku',
			'edit_link' => $productsPATH.'/product/edit',
			'priv'      => 'perch_shop.products.edit',
		]);



		$Listing->add_col([
			'title'     => 'Title',
			'value'     => 'title',
			'sort'      => 'title',
		]);
		
		
		
		$Listing->add_col([
			'title'     => 'Stock',
			'sort' => 'stock_level',
			'value'     => function($Item) use($productsPATH, $Settings) {
				$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
				$stock_location = $stock_status = '';

				if(isset($dynamic_fields['stock_location'])) $stock_location = $dynamic_fields['stock_location'];
				if(isset($dynamic_fields['stock_status'])) $stock_status = $dynamic_fields['stock_status'];
				

				if($stock_location == '0') return '<a href="'.$productsPATH.'/product/variants/?id='.$Item->productID().'" class="primary">On variants</a>';
				if($stock_status == '0') return 'Unlimited';
				if($stock_status == '5') return 'Discontinued';
				
				
				$css ='';
				$stock = (int)$Item->stock_level();
				
				if($stock_status == '3' || $stock == 0) {
					$css = "listing__alert";
				} else if($stock <= (int)$Settings->get('pipit_catalog_lowStok')->val() || $stock_status == '2') {
					$css = "listing__warning";
				}
				
				$stock = '<span class='.$css.'>'.$stock.'</span>';
				return $stock;
			},
		]);



		$sort_price = 'price';
		$displaySale = false;
		if($Settings->get('pipit_catalog_displaySalePrices')->val()) {
			$displaySale = true;
			if($selected_sale === 'true') $sort_price = 'sale_price';
			
		}
		
		$Listing->add_col([
			'title' => 'Price',
			'sort' => $sort_price,
			'value' => function($Item) use ($displaySale, $ProductsAPI) {
				//$prices = $Item->price();
				$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
				
				$prices = $dynamic_fields['price'];
				$onSale = $dynamic_fields['on_sale'];
				if($onSale && $displaySale) $prices = $dynamic_fields['sale_price'];
				
					
				if (PerchUtil::count($prices)) {
					if (isset($prices['_default'])) unset($prices['_default']);
					$Currencies = new PerchShop_Currencies($ProductsAPI);
					$out = [];
					$pre = $post = '';

					if($onSale && $displaySale) {
						$pre = '<span class="listing__sale">';
						$post = '</span>';
					}
					

					foreach($prices as $currencyID=>$price) {
						
						$Currency = $Currencies->find((int)$currencyID);
						if ($Currency) {
							if($price == 0) {
								$out[] = '<span class="listing__alert">'.$Currency->get_formatted($price).'</span>';
							} else {
								$out[] = $Currency->get_formatted($price);
							}
						}

					}
					
					return $pre.implode(', ', $out).$post;
				}
				
				return '<span class="listing__warning">-</span>';
			},
		]);

			
			
		$Listing->add_col([
			'title' => 'Active',
			'sort' => 'status',
			'value' => function($Item) {
				$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
				$status = $dynamic_fields['status'];
					
				if($status == '0') return PerchUI::icon('core/cancel', 16, null, 'icon-status-alert');
				return PerchUI::icon('core/circle-check', 16, null, 'icon-status-success');
			},
		]);
			
			

		$Listing->add_delete_action([
			'priv'   => 'perch_shop.products.delete',
			'inline' => true,
			'path'   => $productsPATH.'/product/delete',
		]);


		
	







	if($products_for_paging) {
		echo $Listing->render($products);
	} else {
		echo $search_message;
	}
