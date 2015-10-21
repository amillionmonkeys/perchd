<?php

	

	echo $HTML->subnav($CurrentUser, array(
		array('page'=>array(
					'perch_mailchimp',
					'perch_mailchimp/subscriber',
					'perch_mailchimp/import',
					'perch_mailchimp/settings/webhooks',
			), 'label'=>'Subscribers'),
		array('page'=>array(
					'perch_mailchimp/campaigns',
					'perch_mailchimp/campaigns/view'
			), 'label'=>'Campaigns'),
		// array('page'=>array(
		// 			'perch_mailchimp/data',
		// 			'perch_mailchimp/data/log'
		// 	), 'label'=>'Import status')
	));
?>