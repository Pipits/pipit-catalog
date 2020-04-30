<?php
	include(__DIR__.'/_version.php');
	
	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('pipit_catalog')) {
		$this->register_app('pipit_catalog', 'Catalog', 5, 'Catalog App', PIPIT_CATALOG_VERSION);
		$this->require_version('pipit_catalog', '3.0');
		
		
		$this->add_setting('pipit_catalog_lowStok', 'Highlight low stock when stock', 'text', false);
		
		$this->add_setting('pipit_catalog_hideCat', 'Hide Category filter', 'checkbox', false);
		$this->add_setting('pipit_catalog_hideBrand', 'Hide Brand filter', 'checkbox', false);
		$this->add_setting('pipit_catalog_hideStatus', 'Hide Status filter', 'checkbox', false);
		$this->add_setting('pipit_catalog_hideSale', 'Hide On Sale filter', 'checkbox', false);
		$this->add_setting('pipit_catalog_hideShipping', 'Hide Shipping filter', 'checkbox', false);
		$this->add_setting('pipit_catalog_hideSearch', 'Hide Search Field', 'checkbox', false);
		
		$this->add_setting('pipit_catalog_hideProductImages', 'Hide product images', 'checkbox', false);
		$this->add_setting('pipit_catalog_displaySalePrices', 'Display sale prices', 'checkbox', false);


		
		$API  = new PerchAPI(1.0, 'pipit_catalog');
        $Perch = Perch::fetch();
		$Perch->add_javascript($API->app_path() . "/assets/js/product_preview.js");

		include_once('lib/PipitCatalog_Products.class.php');
	}