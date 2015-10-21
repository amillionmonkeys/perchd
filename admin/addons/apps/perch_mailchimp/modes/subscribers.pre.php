<?php
    
    $HTML = $API->get('HTML');
      

    $Subscribers = new PerchMailchimp_Subscribers($API);
    
    $Paging = $API->get('Paging');
    $Paging->set_per_page(15);

   
	$Lang = $API->get('Lang');

    $listmembers = array();
	    
    $listmembers = $Subscribers->all_members($Paging);
            
    // Install
    if ($listmembers == false) {
    	$Subscribers->attempt_install();
    }

    // Try to update
    if (file_exists('update.php')) include('update.php');


    $api_key = $Settings->get('perch_mailchimp_api_key')->settingValue();
    $list_id = $Settings->get('perch_mailchimp_list_id')->settingValue();

    if (!$api_key || !$list_id) {
        $Alert->set('notice', $Lang->get('Please visit the Settings page and set your MailChimp API key and List ID'));
    }


?>