<?php
    
    $API  = new PerchAPI(1.0, 'pinboard');
    $Lang = $API->get('Lang');
	$this->register_app('pinboard', 'Pinboard');

	$this->add_setting('pinboard_username', 'Pinboard Username', 'text', '');
	$this->add_setting('pinboard_password', 'Pinboard Token', 'text', '');

	$Settings = $API->get('Settings');
	$username = $Settings->get('pinboard_username')->settingValue();
	$password = $Settings->get('pinboard_password')->settingValue();

	