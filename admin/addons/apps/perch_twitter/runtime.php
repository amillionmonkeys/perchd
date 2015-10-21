<?php
    require('PerchTwitter.class.php');
    require('PerchTwitter_Setting.class.php');
	require('PerchTwitter_Settings.class.php');
	require('PerchTwitter_Tweet.class.php');
	require('PerchTwitter_Tweets.class.php');
 	

	/**
	 * 
	 * function called by scripts (eg:a cron job or scheduled task) to upate the Tweets.
	 */
	function perch_twitter_update_tweets()
	{
		$found = 0;
	    
		$API  = new PerchAPI(1.0, 'perch_twitter');
		
		PerchUtil::debug('Updating');
	
		$TwitterSettings = new PerchTwitter_Settings($API);
		$CurrentSettings = $TwitterSettings->find();
	
		if(is_object($CurrentSettings)) {
			$details = $CurrentSettings->to_array();
		
			$Twitter = new PerchTwitter();	
        	
        	//update tweets
        	$twitter_id_str = $details['settingTwitterID'];
			$twitter_id_array = explode(',',$twitter_id_str);
			
			for($i=0;$i<sizeOf($twitter_id_array); $i++) {	
	    		$twitter_id = trim($twitter_id_array[$i]);        	
	        	$found += $Twitter->get_tweets('favorites', $twitter_id);
		    	$found += $Twitter->get_tweets('mine', $twitter_id);        
			}
		}
		
		return $found;
	}

	/**
	 * Post scheduled tweets to twitter.
	 * @return [type] [description]
	 */
	function perch_twitter_post_tweets()
	{
		$found = 0;
	    
		$API  = new PerchAPI(1.0, 'perch_twitter');
		$Twitter = new PerchTwitter($API);	
		
		$found = $Twitter->post_scheduled_tweets();
		
		return $found;
	}

	
	/**
	 * replacement for above to use opts array
	 * @param array $opts
	 * @param bool $return
	 * @return array or string
	 */
	function perch_twitter_get_latest($opts=array(), $return=false)
	{
		if (!is_array($opts)) $opts = array();

		$twitter_id = false;
		$type		= 'mine';

		if(isset($opts['twitter_id']) && $opts['twitter_id']!= '') {
			$twitter_id = $opts['twitter_id'];
		}
		
		if(isset($opts['type'])) {
			$type = $opts['type'];
		}

		$defaults = array(
			'count'=>3,
			'link_urls'=>true
			);

		$opts = array_merge($defaults, $opts);
		
		$API  	= new PerchAPI(1.0, 'perch_twitter');
	
		$Tweets = new PerchTwitter_Tweets($API);
		
		$r = $Tweets->get_custom($twitter_id, $type, $opts);
	
		if ($return) return $r;
		echo $r;
	}
	

	
	/**
	 * replacement for above to use opts array
	 * @param array $opts
	 * @param bool $return
	 * @return array or string
	 */
	function perch_twitter_get_random($opts=array(), $return=false) {
	
		$API  = new PerchAPI(1.0, 'perch_twitter');
	
		$Tweets = new PerchTwitter_Tweets($API);
	
		if(isset($opts['twitter_id']) && $opts['twitter_id']!= '') {
			$twitter_id = $opts['twitter_id'];
		}else{
			$twitter_id = false;
		}
		
		if(!isset($opts['type'])) {
			$type='mine';
		}else{
			$type = $opts['type'];
		}
		
		$opts['order'] = 'RAND()';
	
		
	
		$r = $Tweets->get_custom($twitter_id,$type, $opts);
	
		if ($return) return $r;
		echo $r;
	}

?>