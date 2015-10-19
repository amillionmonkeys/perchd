<?php
    
    $Members = new PerchMembers_Members($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Member = $Members->find($_GET['id']);
	}
	
	
	if (!is_object($Member)) PerchUtil::redirect($API->app_path());
	
	
	$Form->set_name('delete');

    if ($Form->submitted()) {
    	if (is_object($Member)) {
    	    $Member->delete();
    	    
    	    if ($Form->submitted_via_ajax) {
    	        echo $API->app_path().'/';
    	        exit;
    	    }else{
    	       PerchUtil::redirect($API->app_path().'/'); 
    	    }
    	    
            
        }else{
            $message = $HTML->failure_message('Sorry, that member could not be deleted.');
        }
    }

    
    
    $details = $Member->to_array();

