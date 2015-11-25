<?php
    # include the API
    include('../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'listing');
    $Lang = $API->get('Lang');

    # include your class files
	include('Listings.class.php');
	include('Listing.class.php');

    # Set the page title
    $Perch->page_title = $Lang->get('Manage Member Listings');


    # Do anything you want to do before output is started
    include('modes/list.pre.php');


    # Top layout
    include(PERCH_CORE . '/inc/top.php');


    // # Display your page
    include('modes/list.post.php');


    // # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
