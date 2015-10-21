<?php
include(dirname(__FILE__) . '/../../../../runtime.php');
    
$API      = new PerchAPI(1.0, 'perch_mailchimp');
$Settings = $API->get('Settings');
$db       = $API->get('DB');

$secret = $Settings->get('perch_mailchimp_secret')->settingValue();

if($_POST) {
	//check the secret on the querystring
	if(!isset($_GET['perch'])) {
		die();
	}elseif(isset($_GET['perch']) && $_GET['perch'] != $secret) {
		die();
	}else {

		//process response
		$data = array(
            'subscriberEmail'=>$_POST['data']['email'],
            'subscriberDate'=>$_POST['fired_at']
		);

		$log = array(
            'logEvent'=>'Added subscriber: '. $_POST['data']['email'],
            'logDate'=>date('Y-m-d H:i:s')
		);

		//add user to database
		if($db->insert(PERCH_DB_PREFIX.'mailchimp_subscribers',$data)) {
			$log['logType'] = 'success';
		}else{
			$log['logType'] = 'failure';
		}

		//add to log
		$db->insert(PERCH_DB_PREFIX.'mailchimp_log',$log);
	}

}