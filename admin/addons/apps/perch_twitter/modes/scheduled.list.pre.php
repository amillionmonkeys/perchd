<?php
    $Paging = new PerchPaging();
    $Paging->set_per_page(24);

    $API  = new PerchAPI(1.0, 'perch_twitter');
    $HTML = $API->get('HTML');

    $ScheduledTweets = new PerchTwitter_ScheduledTweets;

    $state = PerchUtil::get('filter', 'unsent');

 	switch($state) {
 		case 'sent' :
 			$tweets = $ScheduledTweets->all_sent($Paging);
 			break;

 		case 'unsent' :
 			$tweets = $ScheduledTweets->all_unsent($Paging);
 			break;
 	}


    
