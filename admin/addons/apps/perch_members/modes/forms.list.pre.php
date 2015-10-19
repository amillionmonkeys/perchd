<?php
    
    $HTML = $API->get('HTML');

    $Members = new PerchMembers_Members($API);
    $MemberForms = new PerchMembers_Forms($API);
    
        
    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);   
   
	$Lang = $API->get('Lang');

    $forms = $MemberForms->all();
