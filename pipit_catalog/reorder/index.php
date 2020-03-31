<?php
    # includes
    include('../../../../core/inc/api.php');
    include('../lib/PipitCatalog_Helper.class.php');

    $API  = new PerchAPI(1.0, 'pipit_catalog');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Products: Reorder Products');

    # Do anything you want to do before output is started
    include('../modes/_subnav.php');
    include('../modes/reorder.pre.php');
    $Perch->add_css($API->app_path().'/assets/css/styles.css');
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');
 
    # Display your page
    include('../modes/reorder.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
