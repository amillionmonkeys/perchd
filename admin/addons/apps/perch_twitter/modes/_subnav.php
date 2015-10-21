<?php
	echo $HTML->subnav($CurrentUser, array(
		array('page'=>array(
					'perch_twitter'
			), 'label'=>'Tweets'),
		array('page'=>array(
					'perch_twitter/scheduled',
					'perch_twitter/scheduled/edit',
					'perch_twitter/scheduled/delete'
			), 'label'=>'Scheduled'),
		array('page'=>array(
					'perch_twitter/settings'
			), 'label'=>'Settings', 'priv'=>'perch_twitter.settings')
	));