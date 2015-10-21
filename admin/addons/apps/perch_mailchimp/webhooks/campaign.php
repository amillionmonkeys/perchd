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
		$status= $_POST['data']['status'];
		$campaign_id= $_POST['data']['id'];

		if($status == 'sent') {
			$log = array(
	            'logEvent'=>'Campaign sent: '. $campaign_id,
	            'logDate'=>date('Y-m-d H:i:s')
			);

			$api_key = $Settings->get('perch_mailchimp_api_key')->settingValue();
			$list_id = $Settings->get('perch_mailchimp_list_id')->settingValue();
			$MailChimp = new MailChimp($api_key);

			$opts = array(
				'apikey'=>$api_key,
				'filters'=>array(
					'campaign_id'=>$campaign_id
				)
			);

			$new_campaign = $MailChimp->call('campaigns/list',$opts);
			if($new_campaign) {

				//get the content
                $content_opts = array(
                    'apikey'=>$api_key,
                    'cid'=>$campaign_id
                );
                $content = $MailChimp->call('campaigns/content',$content_opts);
                
                if(isset($content['html'])) {
                    $campaignHTML = $content['html'];
                }
                if(isset($content['text'])) {
                    $campaignText = $content['text'];
                }

				$campaign = array(
					'campaignCID'        => $campaign_id,
					'campaignWebID'      => $new_campaign['data'][0]['web_id'],
					'campaignTitle'      => $new_campaign['data'][0]['title'],
					'campaignCreateTime' => $new_campaign['data'][0]['create_time'],
					'campaignSendTime'   => $new_campaign['data'][0]['send_time'],
					'campaignSent'       => $new_campaign['data'][0]['emails_sent'],
					'campaignSubject'    => $new_campaign['data'][0]['subject'],
					'campaignArchiveURL' => $new_campaign['data'][0]['archive_url'],
					'campaignHTML'       => $campaignHTML,
					'campaignText'       => $campaignText,
					'campaignSlug'       => PerchUtil::urlify(date('d M Y', strtotime($new_campaign['data'][0]['create_time'])).' '.$new_campaign['data'][0]['subject']),
                );

                //insert into our table
                if($db->insert(PERCH_DB_PREFIX.'mailchimp_campaigns', $campaign)) {
                	$log['logType'] = 'success';
                }else{
					$log['logType'] = 'failure';
				}

			//add to log
				$db->insert(PERCH_DB_PREFIX.'mailchimp_log',$log);
            }

			
		}
	}

}