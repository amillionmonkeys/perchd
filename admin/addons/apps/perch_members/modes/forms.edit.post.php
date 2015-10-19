<?php
    # Side panel
    echo $HTML->side_panel_start();   
    echo $HTML->side_panel_end();
        
    # Main panel
    echo $HTML->main_panel_start(); 

    include('_subnav.php');

    echo $HTML->heading1($heading1);
    
    if ($message) echo $message;    
    
    
    echo $HTML->heading2('Form details');
    
    
    echo $Form->form_start(false, 'magnetic-save-bar');
    
        echo $Form->text_field('formTitle', 'Title', isset($details['formTitle'])?$details['formTitle']:false, 'l');

        echo $HTML->heading2('Options');

        echo $Form->checkbox_field('moderate', 'New members require approval', '1', isset($settings['moderate'])?$settings['moderate']:'1');
        echo $Form->text_field('moderator_email', 'Email address of moderator', isset($settings['moderator_email'])?$settings['moderator_email']:false, 'l');
        echo $Form->text_field('default_tags', 'Default tags', isset($settings['default_tags'])?$settings['default_tags']:false, 'l');

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    
    echo $Form->form_end();
    
    echo $HTML->main_panel_end();
