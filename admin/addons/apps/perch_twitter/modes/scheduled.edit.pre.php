<?php

	$API = new PerchAPI(1.0, 'perch_twitter');
	$HTML = $API->get('HTML');

	$Tweets 		= new PerchTwitter_ScheduledTweets;

	$tweetID   = false;
	$Tweet     = false;
	$message = false;

	if (PerchUtil::get('id')) {
		$tweetID    = (int) PerchUtil::get('id');
		$Tweet      = $Tweets->find($tweetID);
	}

	if (!$CurrentUser->has_priv('perch_twitter.schedule')) {
		PerchUtil::redirect($API->app_path());
	}

	$Form = $API->get('Form');

    if ($Form->submitted()) {		
    	
    	$data = $Form->receive(array('tweetStatus', 'mark_as_unsent'));

        $data['tweetSendDate'] = $Form->get_date('tweetSendDate', $_POST);

        PerchUtil::debug($data);

   

        if (!is_object($Tweet)) {

            if (isset($data['mark_as_unsent'])) unset($data['mark_as_unsent']);

            $Tweet = $Tweets->create($data);
            PerchUtil::redirect($API->app_path() .'/scheduled/edit/?id='.$Tweet->id().'&created=1');
        }

        if (isset($data['mark_as_unsent'])) {

            if ($data['mark_as_unsent']=='1') {
                $data['tweetSent'] = '0';    
            }
            
            unset($data['mark_as_unsent']);
        }

        $Tweet->update($data);
    	
        if (is_object($Tweet)) {
            $message = $HTML->success_message('Your tweet has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path() .'/scheduled/">', '</a>');
        }else{
            $message = $HTML->failure_message('Sorry, that tweet could not be edited.');
        }
      
    } 

    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your tweet has been successfully scheduled. Return to %slisting%s', '<a href="'.$API->app_path() .'/scheduled/">', '</a>');
    }
