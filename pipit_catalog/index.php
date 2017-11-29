<?php
    # include the API
    include('../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'pipit_catalog');
	$HTML   = $API->get('HTML');
	$Lang   = $API->get('Lang');
	$Paging = $API->get('Paging');
	
    # Set the page title
    $Perch->page_title = $Lang->get('Catalog app');

    include('modes/_subnav.php');
	
    # Do anything you want to do before output is started
    include('modes/list.pre.php');
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');
?>
	<link rel="stylesheet" href="assets/vendor/choices/styles/css/choices.min.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<script src="assets/vendor/choices/scripts/dist/choices.min.js"></script>
	<script src="assets/js/script.js"></script>
<?php
    
    # Display your page
    include('modes/list.post.php');
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
