<?php
	if ($CurrentUser->logged_in()) {
    	$this->register_app('perch_twitter', 'Twitter', 10, 'App to display and post Tweets', '3.5.1');
    	$this->require_version('perch_twitter', '2.7.4');
    }
