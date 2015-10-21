<?php
    
    $HTML = $API->get('HTML');
    
    
    $Campaigns = new PerchMailchimp_Campaigns($API);
    



	
   
	$Lang = $API->get('Lang');

    if (isset($_GET['id']) && $_GET['id']!='') {
        $campaignID = (int) $_GET['id'];    
        $Campaign = $Campaigns->find($campaignID, true);
        $details = $Campaign->to_array();
            
    }else{
        
        PerchUtil::redirect($API->app_path());
       
    }

            
    

?>