<?php
    # include the API
    include('../../../core/inc/api.php');
    include('lib/PipitCatalog_Helper.class.php');
    
    $API  = new PerchAPI(1.0, 'pipit_catalog');
	$HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');
    $Form = $API->get('Form');
    $Paging = $API->get('Paging');
    $Settings = $API->get('Settings');

	if ($Settings->get('pipit_catalog_update')->val()!=PIPIT_CATALOG_VERSION) {
    	PerchUtil::redirect($API->app_path().'/update/');
	}
	
    # Set the page title
    $Perch->page_title = $Lang->get('Manage Products');

    # Do anything you want to do before output is started
    include('modes/_subnav.php');
    include('modes/list.pre.php');
    $Perch->add_css($API->app_path().'/assets/vendor/choices/styles/css/choices.min.css');
    $Perch->add_css($API->app_path().'/assets/css/styles.css');
    $Perch->add_javascript($API->app_path().'/assets/vendor/choices/scripts/dist/choices.min.js');
    $Perch->add_javascript($API->app_path().'/assets/js/script.js');

    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    # Display your page
    include('modes/list.post.php');
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
