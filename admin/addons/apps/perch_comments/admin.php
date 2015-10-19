<?php
	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_comments')) {
	    $this->register_app('perch_comments', 'Comments', 2, 'A comments system', '1.2');
	    $this->require_version('perch_comments', '2.8.11');
	    $this->add_setting('perch_comments_akismet_key', 'Akismet API key', 'text', '');
	    $this->add_setting('perch_comments_max_spam_days', 'Delete spam comments', 'select', '0', array(
	    		array('label'=>'Never', 'value'=>'0'),
	    		array('label'=>'After 1 day', 'value'=>'1'),
	    		array('label'=>'After 7 days', 'value'=>'7'),
	    		array('label'=>'After 14 days', 'value'=>'14'),
	    		array('label'=>'After 30 days', 'value'=>'30'),
	    		array('label'=>'After 60 days', 'value'=>'60'),
	    		array('label'=>'After 90 days', 'value'=>'90'),
	    	));
	}
