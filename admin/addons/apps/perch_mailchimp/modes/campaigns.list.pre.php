<?php
    
    $HTML = $API->get('HTML');
    
    
    $Campaigns = new PerchMailchimp_Campaigns($API);
    

    
    $Paging = $API->get('Paging');
    $Paging->set_per_page(15);


    
   
    $Lang = $API->get('Lang');

    $list = array();
    
    
    $list = $Campaigns->all_campaigns($Paging);

            
    

?>