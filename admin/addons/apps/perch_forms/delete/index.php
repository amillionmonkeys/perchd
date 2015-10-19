<?php
    # include the API
    include('../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_forms');
    $Lang = $API->get('Lang');

    # include your class files
    include('../PerchForms_Forms.class.php');
    include('../PerchForms_Form.class.php');
    include('../PerchForms_Responses.class.php');
    include('../PerchForms_Response.class.php');

    # Set the page title
    $Perch->page_title = $Lang->get('Forms');

    # Do anything you want to do before output is started
    include('../modes/form.delete.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../modes/form.delete.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>