<?php
    
    $HTML = $API->get('HTML');
    
    
    $Subscribers = new PerchMailchimp_Subscribers($API);
    



	
   
	$Lang = $API->get('Lang');

    if (isset($_GET['id']) && $_GET['id']!='') {
        $subscriberID = (int) $_GET['id'];    
        $Subscriber = $Subscribers->find($subscriberID, true);
        $details = $Subscriber->to_array();
            
    }else{
        
        PerchUtil::redirect($API->app_path());
       
    }

            
    

?>