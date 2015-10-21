<?php
	
	$API  = new PerchAPI(1.0, 'perch_twitter');

	if (!class_exists('PerchTwitter_Settings')) {
		include('PerchTwitter_Settings.class.php');
		include('PerchTwitter_Setting.class.php');
	}

	$TwitterSettings = new PerchTwitter_Settings($API);
	$CurrentSettings = $TwitterSettings->find();

	PerchScheduledTasks::register_task('perch_twitter', 'post_tweets', 1, 'scheduled_post_tweets');



	if ((int)$CurrentSettings->settingUpdateInterval()!=0) {
		$interval = (int)$CurrentSettings->settingUpdateInterval();

		PerchScheduledTasks::register_task('perch_twitter', 'update_tweets', $interval, 'scheduled_get_tweets');
	}


	function scheduled_get_tweets($last_run)
	{
		$count = perch_twitter_update_tweets();

		if ($count == 1) {
			$tweets = 'tweet';
		}else{
			$tweets = 'tweets';
		}

		return array(
				'result'=>'OK',
				'message'=>$count.' new '.$tweets.' fetched.'
			);
	}


	function scheduled_post_tweets($last_run)
	{
		$count = perch_twitter_post_tweets();

		if ($count == 1) {
			$tweets = 'tweet';
		}else{
			$tweets = 'tweets';
		}

		return array(
				'result'=>'OK',
				'message'=>$count.' new '.$tweets.' posted.'
			);
	}


