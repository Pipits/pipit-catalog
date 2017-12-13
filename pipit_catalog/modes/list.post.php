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
	$ParentSmartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
	
	
	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'products';
	}
	$ParentSmartbar->add_item([
        'active' => $smartbar_selection=='products',
        'title' => $Lang->get('Products'),
        'link'  => $URL,
        'icon'  => 'ext/o-shirt',
    ]);

	
    if(!$Settings->get('pipit_catalog_hideSearch')->val())
	{
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

    echo $ParentSmartbar->render();
	


	# Filters 
	// form->start()
	echo '<form method="get" action="'.$API->app_path().'" class="app form-simple pipit-filters">';
	
		if(!$Settings->get('pipit_catalog_hideCat')->val() && $Settings->get('pipit_catalog_productsSet')->val() && PerchUtil::count($Categories)) 
		{
			$cat_opts = [];
			$cat_opts[] = ['value' => '', 'label' => ''];
			foreach($Categories as $Category) {
				$catID = $Category->catID();
				$setID = $Category->setID();
				
				$cat_opts[] = [
					'value' => $catID,
					'setID' => $setID,
					'label' => $Category->catTitle(),
				];
			}
			
			echo $Form->select_field('category', 'Categories', $cat_opts, $selected_catID, 'pipit-filters__choices');
		}
		
		
		if(!$Settings->get('pipit_catalog_hideBrand')->val() && PerchUtil::count($brands)) 
		{
			$brand_opts = [];
			$brand_opts[] = ['value' => '', 'label' => ''];
			foreach($brands as $brand) {
				$brandID = $brand->brandID();
				
				$brand_opts[] = [
					'value' => $brandID,
					'brandID' => $brandID,
					'title' => $brand->brandTitle(),
					'label' => $brand->brandTitle(),
				];
			}
			
			echo $Form->select_field('brand', 'Brands', $brand_opts, $selected_brandID, 'pipit-filters__choices');
		}
		
	
		if(!$Settings->get('pipit_catalog_hideStatus')->val())
		{
			$status_opts = [];
			$status_opts[] = ['value' => '', 'label' => ''];
			
			$status_opts[] = [
				'label' => 'Inactive',
				'value' => 'false',
			];
			$status_opts[] = [
				'label' => 'Active',
				'value' => 'true',
			];
			
			echo $Form->select_field('active', 'Status', $status_opts, $selected_status, 'pipit-filters__choices');
		}
				
		
		if(!$Settings->get('pipit_catalog_hideSale')->val())
		{		
			$sale_opts = [];
			$sale_opts[] = ['value' => '', 'label' => ''];
			
			$sale_opts[] = [
				'label' => 'Yes',
				'value' => 'true',
			];
			$sale_opts[] = [
				'label' => 'No',
				'value' => 'false',
			];
			
			echo $Form->select_field('sale', 'On Sale', $sale_opts, $selected_sale, 'pipit-filters__choices');
		}
		
		
		if(!$Settings->get('pipit_catalog_hideShipping')->val())
		{
			$shipping_opts = [];
			$shipping_opts[] = ['value' => '', 'label' => ''];
			
			$shipping_opts[] = [
				'label' => 'Required',
				'value' => 'true',
			];
			
			$shipping_opts[] = [
				'label' => 'No Shipping',
				'value' => 'false',
			];
			
			echo $Form->select_field('shipping', 'Shipping', $shipping_opts, $selected_shipping, 'pipit-filters__choices');
		}
	
	
		echo '<div class="submit-bar-actions"><input type="submit" id="btnSubmit" value="Filter" class="button button-simple"></div>';
    echo $Form->form_end();
	
	
    
	
	if($products_for_paging)
	{
		$Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
		
		if(!$Settings->get('pipit_catalog_hideProductImages')->val())
		{
			/*
			*	default values in template
			*	$thumb_w = 'w80';
			*	$thumb_h = 'h80';
			*	$thumb_crop = 'c1';
			*	$thumb_density = '@1.6x';
			*/
			
			$thumb_w = $thumb_h = $thumb_density = '';
			$thumb_crop = 'c0';
			
			if($Settings->get('pipit_catalog_thumbW')->val())
			{
				$thumb_w = 'w'.$Settings->get('pipit_catalog_thumbW')->val();
			}
			
			if($Settings->get('pipit_catalog_thumbH')->val())
			{
				$thumb_h = 'h'.$Settings->get('pipit_catalog_thumbH')->val();
			}
			
			if($Settings->get('pipit_catalog_thumbCrop')->val())
			{
				$thumb_crop = 'c1';
			}
			
			if($Settings->get('pipit_catalog_thumbDensity')->val())
			{
				$thumb_density = '@'.$Settings->get('pipit_catalog_thumbDensity')->val().'x';
			}
			
			$thumb_size = $thumb_w.$thumb_h.$thumb_crop.$thumb_density;
			
			$Listing->add_col([
				'title'     => 'Image',
				'value'     => function($Item)
				{
					$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
					global $API;
					$noImg = '<img class="listing__thumb" src="'.$API->app_path().'/assets/images/no-image.png'.'" />';
					
					if(isset($dynamic_fields['image']))
					{
						$image = $dynamic_fields['image'];
						
						$default_path = $image['_default'];
						$default_name = basename($default_path);
						
						
						global $thumb_size;
						if(array_key_exists($thumb_size, $image['sizes']))
						{
							$thumb_name = $image['sizes'][$thumb_size]['path'];							
							$thumb_path = str_replace($default_name, $thumb_name, $default_path);
							return '<img class="listing__thumb" src="'.$thumb_path.'" />';
						}
						else if(array_key_exists('thumb', $image['sizes']))
						{
							$thumb_name = $image['sizes']['thumb']['path'];							
							$thumb_path = str_replace($default_name, $thumb_name, $default_path);
							return '<img class="listing__thumb" src="'.$thumb_path.'" />';
						}
						else
						{
							return $noImg;
						}
					}
					else
					{
						return $noImg;
					}
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
			'value'     => function($Item) {
				$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
				$stock_location = $stock_status = '';
				if(isset($dynamic_fields['stock_location']))
				{
					$stock_location = $dynamic_fields['stock_location'];
				}
				if(isset($dynamic_fields['stock_status']))
				{
					$stock_status = $dynamic_fields['stock_status'];
				}


				if($stock_location == '0')
				{
					global $productsPATH;
						
					return '<a href="'.$productsPATH.'/product/variants/?id='.$Item->productID().'" class="primary">On variants</a>';
				}
				else if($stock_status == '0')
				{
					return 'Unlimited';
				}
				else if($stock_status == '5')
				{
					return 'Discontinued';
				}
				else
				{
					global $Settings;
					$css ='';
					$stock = (int)$Item->stock_level();
					
					if($stock_status == '3' || $stock == 0)
					{
						$css = "listing__alert";
					}
					else if($stock <= (int)$Settings->get('pipit_catalog_lowStok')->val() || $stock_status == '2')
					{
						$css = "listing__warning";
					}
					
					$stock = '<span class='.$css.'>'.$stock.'</span>';
					return $stock;					
				}
			},
		]);



		$sort_price = 'price';
		$displaySale = false;
		if($Settings->get('pipit_catalog_displaySalePrices')->val())
		{
			$displaySale = true;
			if(isset($_GET['sale']) && $_GET['sale'] === 'true')
			{
				$sort_price = 'sale_price';
			}
		}
		
		$Listing->add_col([
			'title' => 'Price',
			'sort' => $sort_price,
			'value' => function($Item) use ($HTML) {
				global $displaySale;
				
				//$prices = $Item->price();
				$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
				
				$prices = $dynamic_fields['price'];
				$onSale = $dynamic_fields['on_sale'];
				if($onSale && $displaySale)
				{					 
					$prices = $dynamic_fields['sale_price'];
				}
					
				if (PerchUtil::count($prices)) {
					if (isset($prices['_default'])) unset($prices['_default']);    
					global $ProductsAPI;
					$Currencies = new PerchShop_Currencies($ProductsAPI);
					$out = [];
					$pre = $post = '';

					if($onSale && $displaySale)
					{
						$pre = '<span class="listing__sale">';
						$post = '</span>';
					}
					
					foreach($prices as $currencyID=>$price) {
						
						$Currency = $Currencies->find((int)$currencyID);
						if ($Currency) {
							if($price == 0)
							{
								$out[] = '<span class="listing__alert">'.$Currency->get_formatted($price).'</span>';
							}
							else
							{
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
			'value' => function($Item){
				$dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
				$status = $dynamic_fields['status'];
					
				if($status == '0')
				{
					return PerchUI::icon('core/cancel', 16, null, 'icon-status-alert');
				}
				else
				{
					return PerchUI::icon('core/circle-check', 16, null, 'icon-status-success');
				}
			},
		]);
			
			

		$Listing->add_delete_action([
			'priv'   => 'perch_shop.products.delete',
			'inline' => true,
			'path'   => $productsPATH.'/product/delete',
		]);


		
		echo $Listing->render($products);
	}
	else
	{
		echo $search_message;
	}
?>