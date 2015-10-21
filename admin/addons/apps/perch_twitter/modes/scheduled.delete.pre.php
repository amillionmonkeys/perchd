<?php

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');

    if (!$CurrentUser->has_priv('perch_twitter.schedule')) {
        PerchUtil::redirect($API->app_path() . '/scheduled/');
    }

    $Tweets  = new PerchTwitter_ScheduledTweets;

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $tweetID  = (int) $_GET['id'];
        
        $Tweet = $Tweets->find($tweetID);
    }

    if (!$Tweet || !is_object($Tweet)) {
        PerchUtil::redirect($API->app_path() . '/scheduled/');
    }

    /* --------- Delete Form ----------- */
    
    $Form = new PerchForm('delete');
    
    if ($Form->posted() && $Form->validate()) {
        
        $Tweet->delete();
        
        if ($Form->submitted_via_ajax) {
    	    echo $API->app_path() . '/scheduled/';
    	    exit;
    	}else{
    	    PerchUtil::redirect($API->app_path() . '/scheduled/');
    	}
           	
    	
    }

