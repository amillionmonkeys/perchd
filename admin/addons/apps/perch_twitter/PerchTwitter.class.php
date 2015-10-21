<?php

class PerchTwitter
{
    protected $table     = 'twitter_settings';
	protected $pk        = 'settingID';
	protected $singular_classname = 'PerchTwitter_Setting';
	
	//private static $api_url = 'http://twitter.com/';
	private static $api_url = 'https://api.twitter.com/1/';
	private static $cache_time = 1800; // 60 secs * 30 minutes
	

    /**
     * 
     * Gets the statuses from Twitter for a user account
     * @param string $userID account name
     * @param int $count
     */
	public function get_statuses($userID, $count=200)
	{
		if (!class_exists('tmhOAuth')) {
			require('tmhOAuth/tmhOAuth.php');
	    	require('tmhOAuth/tmhUtilities.php');
		}

		$TwitterSettings = new PerchTwitter_Settings();
		$CurrentSettings = $TwitterSettings->find();

		$tmhOAuth = new tmhOAuth(array(
	        'consumer_key'    => $CurrentSettings->settingTwitterKey(),
	        'consumer_secret' => $CurrentSettings->settingTwitterSecret(),
			'user_token'      => $CurrentSettings->settingTwitterToken(),
	  		'user_secret'     => $CurrentSettings->settingTwitterTokenSecret()
        ));

		$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline.json'), array(
				'screen_name'=>$userID,
				'count'=>$count
			));

		if ($code == 200) {
		  return PerchUtil::json_safe_decode($tmhOAuth->response['response']);
		} 
		
		return false;
	}
	
	/**
     * 
     * Gets the favorites from Twitter for a user account
     * @param string $userID account name
     * @param int $count
     */
	public function get_favorites($userID, $count=200)
	{
		PerchUtil::debug("getting favourites");

		if (!class_exists('tmhOAuth')) {
			require('tmhOAuth/tmhOAuth.php');
	    	require('tmhOAuth/tmhUtilities.php');
		}

		$TwitterSettings = new PerchTwitter_Settings();
		$CurrentSettings = $TwitterSettings->find();

		$tmhOAuth = new tmhOAuth(array(
	        'consumer_key'    => $CurrentSettings->settingTwitterKey(),
	        'consumer_secret' => $CurrentSettings->settingTwitterSecret(),
			'user_token'      => $CurrentSettings->settingTwitterToken(),
	  		'user_secret'     => $CurrentSettings->settingTwitterTokenSecret()
        ));

		$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/favorites/list.json'), array(
				'screen_name'=>$userID,
				'count'=>$count
			));

		if ($code == 200) {
		  return PerchUtil::json_safe_decode($tmhOAuth->response['response']);
		}else{
			PerchUtil::debug('Getting favourites failed.');
			PerchUtil::debug($tmhOAuth);
		}
		
		return false;
	}
	
	/**
	 * 
	 * called whenever we want to update the Tweets from Twitter
	 * @param string $type favorites|mine
	 * @param string $twitter_id the account from which to retrieve Tweets
	 */
	public function get_tweets($type,$twitter_id) 
	{
	    $found = 0;
	    
		if($type == 'favorites') {
			$tweets = $this->get_favorites($twitter_id);
		} else {
			$type = 'mine';
			$tweets = $this->get_statuses($twitter_id);
		}
		
		$PerchTwitterTweets  = new PerchTwitter_Tweets();
		
		//get list of existing Tweet IDs for comparison and deduping.
	    $a = $PerchTwitterTweets->get_tweet_ids($twitter_id,$type);
	        
		if (PerchUtil::count($tweets)) {
			foreach($tweets as $tweet) {
			    //loop through all retrieved Tweets
			    
			    $formatted_tweet_id = trim($tweet->id_str);
			    
				if(!in_array($formatted_tweet_id,$a)) {
					$data = array();
				    $data['tweetTwitterID']    = $formatted_tweet_id;
				    $data['tweetUser']         = trim($tweet->user->screen_name);
				    $data['tweetUserRealName'] = trim($tweet->user->name);
				    $data['tweetUserAvatar']   = trim($tweet->user->profile_image_url);
				    $data['tweetDate']         = date('Y:m:d H:i:s', strtotime($tweet->created_at));
				    $data['tweetTimeOffset']   = (int) $tweet->user->utc_offset;
				    $data['tweetText']         = trim($tweet->text);
				    $data['tweetHTML']         = $this->get_tweet_html($tweet);
				    $data['tweetType']		   = $type;
				    $data['tweetAccount']	   = $twitter_id;

				    // set a flag if this is an @reply, we can then exclude these at runtime if desired
			        if($tweet->in_reply_to_user_id !='') {
				    	$data['tweetIsReply'] = 1;
				    }
					//create a single Tweet entry in the database            
				    $PerchTwitterTweets->create($data);
				    $found++;
			    }else{
			    	PerchUtil::debug('<br />match: '. $tweet->id);
			    }
			}
		}
		
		return $found;
	}


	public function get_tweet_html($tweet)
	{
		$text = trim($tweet->text);

		if ($tweet->entities) {

			// URLs
			if ($tweet->entities->urls) {
				foreach($tweet->entities->urls as $URL) {
					$replacement = '<a href="'.PerchUtil::html($URL->expanded_url, true).'">'.PerchUtil::html($URL->display_url).'</a>';
					$text = str_replace($URL->url, $replacement, $text);
				}
			}

			// Media
			if (isset($tweet->entities->media)) {
				foreach($tweet->entities->media as $URL) {
					$replacement = '<a href="'.PerchUtil::html($URL->expanded_url, true).'">'.PerchUtil::html($URL->display_url).'</a>';
					$text = str_replace($URL->url, $replacement, $text);
				}
			}

			// mentions
			if ($tweet->entities->user_mentions) {
				foreach($tweet->entities->user_mentions as $Mention) {
					$replacement = ' <a href="http://twitter.com/'.PerchUtil::html($Mention->screen_name, true).'">@'.PerchUtil::html($Mention->screen_name).'</a> ';
					$text = str_replace('@'.$Mention->screen_name, $replacement, $text);
				}
			}

			// hashtags
			if ($tweet->entities->hashtags) {
				foreach($tweet->entities->hashtags as $Hashtag) {
					$replacement = '<a href="http://twitter.com/search?q='.PerchUtil::html(urlencode('#'.$Hashtag->text), true).'">#'.PerchUtil::html($Hashtag->text).'</a>';
					$text = str_replace('#'.$Hashtag->text, $replacement, $text);
				}
			}

		}

		return $text;
	}

	public function post_scheduled_tweets()
	{
		if (!class_exists('tmhOAuth')) {
			require('tmhOAuth/tmhOAuth.php');
	    	require('tmhOAuth/tmhUtilities.php');
		}

		$Tweets  = new PerchTwitter_Tweets();

		$tweets = $Tweets->get_scheduled_tweets();

		$sent = 0;

		if (PerchUtil::count($tweets)) {

			$TwitterSettings = new PerchTwitter_Settings();
			$CurrentSettings = $TwitterSettings->find();

			$tmhOAuth = new tmhOAuth(array(
		        'consumer_key'    => $CurrentSettings->settingTwitterKey(),
		        'consumer_secret' => $CurrentSettings->settingTwitterSecret(),
				'user_token'      => $CurrentSettings->settingTwitterToken(),
		  		'user_secret'     => $CurrentSettings->settingTwitterTokenSecret()
	        ));

			foreach($tweets as $tweet) {

				$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update.json'), array(
						'status'=>$tweet['tweetStatus']
					));

				if ($code == 200) {
				  	$sent++;
				  	$Tweets->mark_scheduled_as_sent($tweet['tweetID']);
				}else{
					PerchUtil::debug(PerchUtil::json_safe_decode($tmhOAuth->response['response']));
				}
	
			}

		}

		return $sent;
	}
    
}
