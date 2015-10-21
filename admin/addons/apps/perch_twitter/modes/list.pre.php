<?php
    
    $HTML = $API->get('HTML');
    $Form = $API->get('Form');


    
    // Try to update
    if (file_exists('update.php')) include('update.php');


    $Tweets = new PerchTwitter_Tweets($API);

    $TwitterSettings = new PerchTwitter_Settings($API);
    $TwitterSettings->attempt_install();

    $CurrentSettings = $TwitterSettings->find();
    
    if(!is_object($CurrentSettings)) {
        $TwitterSettings->attempt_install();
        $CurrentSettings = $TwitterSettings->find();
    }
    
    $details = array();
    if ($CurrentSettings) {
        $details = $CurrentSettings->to_array();
    }else{
        $details = false;
    }
    
    
    $message = '';

    
    if ($Form->submitted()) {
        $postvars    = array('settingTwitterID');
    	$data        = $Form->receive($postvars);
    	$Twitter     = new PerchTwitter();	
        	
        //update tweets
        $twitter_id_str = $data['settingTwitterID'];
		$twitter_id_array = explode(',',$twitter_id_str);
			
		for($i=0; $i<PerchUtil::count($twitter_id_array); $i++) {	
	    	$twitter_id = trim($twitter_id_array[$i]);        	
	        $Twitter->get_tweets('favorites', $twitter_id);
		    $Twitter->get_tweets('mine', $twitter_id);        
		}
		
		$message = $HTML->success_message('Tweets updated.');  
    
    }


    if (!function_exists('curl_init')) {
        $message = $HTML->failure_message('You need the <a href="http://www.php.net/manual/en/ref.curl.php">PHP cURL functions</a> enabled in your hosting account to be able to use the Twitter app.');
    }

    
    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);


    $filter = 'all';

    if (isset($_GET['type']) && $_GET['type']=='mine') {
        $filter = 'type';
        $type = 'mine';
    }

    if (isset($_GET['type']) && $_GET['type']=='favorites') {
        $filter = 'type';
        $type = 'favorites';
    }



    switch($filter) {

        case 'type':
            $posts = $Tweets->get_all_by_type($type, $Paging);
            break;

        default:
            $posts = $Tweets->get_all($Paging);
            break;
    }



    if (!PerchUtil::count($posts)) {
        $message .=  $HTML->warning_message('No Tweets found. %sSet or check your Twitter username%s', '<a href="'.$HTML->encode($API->app_path().'/settings/').'">','</a>'); 
    }
    
?>