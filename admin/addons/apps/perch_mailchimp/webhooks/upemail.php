<?php
include(dirname(__FILE__) . '/../../../../runtime.php');
    
$API  = new PerchAPI(1.0, 'perch_mailchimp');
$Settings = $API->get('Settings');
$db = $API->get('DB');

$secret = $Settings->get('perch_mailchimp_secret')->settingValue();

if($_POST) {
	//check the secret on the querystring
	if(!isset($_GET['perch'])) {
		die();
	}elseif(isset($_GET['perch']) && $_GET['perch'] != $secret) {
		die();
	}else {

		//process response
		$old_email= $_POST['data']['old_email'];
		$new_email= $_POST['data']['old_email'];
		$log = array(
            'logEvent'=>'Changed subscriber email from: '. $old_email .' to '. $new_email,
            'logDate'=>date('Y-m-d H:i:s')
		);

		//update database
		if($db->execute('UPDATE '. PERCH_DB_PREFIX.'mailchimp_subscribers SET subscriberEmail = '.$db->pdb($new_email) .' WHERE subscriberEmail = '.$db->pdb($old_email))) {
			$log['logType'] = 'success';
		}else{
			$log['logType'] = 'failure';
		}

		//add to log
		$db->insert(PERCH_DB_PREFIX.'mailchimp_log',$log);
	}

}