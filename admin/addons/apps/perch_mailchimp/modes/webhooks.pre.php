<?php
    
    $HTML = $API->get('HTML');

	$Lang = $API->get('Lang');

            
    
    $Form = $API->get('Form');

    $Settings = $API->get('Settings');
    $MailChimp = new PerchMailchimp_Util($API);

    $api_key = $Settings->get('perch_mailchimp_api_key')->settingValue();
    $list_id = $Settings->get('perch_mailchimp_list_id')->settingValue();


?>