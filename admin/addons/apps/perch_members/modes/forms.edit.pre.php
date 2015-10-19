<?php

    $Members = new PerchMembers_Members($API);

    $MemberForms = new PerchMembers_Forms($API);

    $message = false;
    
    $HTML = $API->get('HTML');

    if (isset($_GET['id']) && $_GET['id']!='') {
        $formID = (int) $_GET['id'];    
        $MemberForm = $MemberForms->find($formID);
        $details = $MemberForm->to_array();
        $settings = PerchUtil::json_safe_decode($MemberForm->formSettings(), true);
    
        $heading1 = 'Editing a Member Form';

    }

    $heading2 = 'Form details';
    



    $Form = $API->get('Form');
    $Form->require_field('formTitle', 'Required');

    if ($Form->submitted()) {
   	        
        $postvars = array('formTitle');
		
    	$data = $Form->receive($postvars);

    	$result = false;
    	
    	
    	if (is_object($MemberForm)) {
    	    
            

            $postvars = array('moderate', 'moderator_email', 'default_tags');
            $settings_data = $Form->receive($postvars);

            if (!isset($settings_data['moderate'])) $settings_data['moderate'] = '0';

            $data['formSettings'] = PerchUtil::json_safe_encode($settings_data);


            $result = $MemberForm->update($data);

            

    	}
    	
        $message = $HTML->success_message('The form has been successfully updated. Return to %sform listing%s', '<a href="'.$API->app_path() .'/forms/">', '</a>');  

        if (is_object($MemberForm)) {
            $details = $MemberForm->to_array();
            $settings = PerchUtil::json_safe_decode($MemberForm->formSettings(), true);
        }
        
    }