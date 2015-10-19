<?php

	PerchScheduledTasks::register_task('perch_comments', 'delete_spam_comments', 1440, 'scheduled_comments_delete_spam');

	function scheduled_comments_delete_spam($last_run)
	{
		$API  = new PerchAPI(1.0, 'perch_comments'); 
		$Settings = $API->get('Settings');

		$days = $Settings->get('perch_comments_max_spam_days')->val();

		if (!$days) return array(
				'result'=>'OK',
				'message'=> 'Spam message deletion not configured.'
			);

		$count = perch_comments_delete_old_spam($days);

		if ($count == 1) {
			$comments = 'comment';
		}else{
			$comments = 'comments';
		}

		return array(
				'result'=>'OK',
				'message'=>$count.' old spam '.$comments.' deleted.'
			);
	}
