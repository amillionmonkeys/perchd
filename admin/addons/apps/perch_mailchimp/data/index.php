<?php
    # include the API
    include('../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_mailchimp');
    $Lang = $API->get('Lang');

    # include your class files
    include('../PerchMailchimp_Subscribers.class.php');
    include('../PerchMailchimp_Subscriber.class.php');

    # Set the page title
    $Perch->page_title = $Lang->get('MailChimp');


    # Do anything you want to do before output is started
    include('../modes/data.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../modes/data.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>