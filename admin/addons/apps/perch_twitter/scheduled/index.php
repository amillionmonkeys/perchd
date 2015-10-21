<?php
    # include the API
    include('../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_twitter');
    
    # Grab an instance of the Lang class for translations
    $Lang = $API->get('Lang');
    
    # include your class files
    include('../PerchTwitter_Settings.class.php');
    include('../PerchTwitter_Setting.class.php');
	include('../PerchTwitter_Tweets.class.php');
    include('../PerchTwitter_Tweet.class.php');
    include('../PerchTwitter_ScheduledTweets.class.php');
    include('../PerchTwitter_ScheduledTweet.class.php');
    include('../PerchTwitter.class.php');

    # Set the page title
    $Perch->page_title = $Lang->get('Twitter app');


    # Do anything you want to do before output is started
    include('../modes/scheduled.list.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../modes/scheduled.list.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
