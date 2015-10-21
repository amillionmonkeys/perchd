<?php

PerchScheduledTasks::register_task('perch_mailchimp', 'update_list_stats', 60, 'update_perch_mailchimp');

  function update_perch_mailchimp($last_update)
  {
    $API  = new PerchAPI(1.0, 'perch_mailchimp');
    include('PerchMailchimp_Stats.class.php');
    include('PerchMailchimp_Stat.class.php');

    $Settings = $API->get('Settings');
    $api_key = $Settings->get('perch_mailchimp_api_key')->settingValue();
    $list_id = $Settings->get('perch_mailchimp_list_id')->settingValue();

    $Stats = new PerchMailchimp_Stats($API);


    if(!$api_key || $api_key == '' || !$list_id || $list_id == '') {
      return array(
          'result'=>'FAILED',
          'message'=>'API key or list ID not provided in Settings.'
        );
    }else{
      if ($Stats->populate($api_key,$list_id)) {
        return array(
          'result'=>'OK',
          'message'=>'List statistics updated.'
        );
      }else{
        return array(
          'result'=>'WARNING',
          'message'=>'Unable to update list statistics.'
        );
      }
    }
  }



?>