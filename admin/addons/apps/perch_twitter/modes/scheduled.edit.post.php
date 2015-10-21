<?php 
    include (PERCH_PATH.'/core/inc/sidebar_start.php');
 
    echo $HTML->para('Update your scheduled tweet.');
 
    include (PERCH_PATH.'/core/inc/sidebar_end.php'); 
    include (PERCH_PATH.'/core/inc/main_start.php'); 
    include ('_subnav.php'); 
 
    
    if ($Tweet) {
        echo $HTML->heading1('Editing a Scheduled Tweet');
    }else{
        echo $HTML->heading1('Adding a New Scheduled Tweet');         
    }
        

    // If a success or failure message has been set, output that here
    echo $message;

    // Sub head
    echo $HTML->heading2('Details');

    // Output the edit form
    echo $Form->form_start();

    $details = array();
    if (is_object($Tweet)) $details = $Tweet->to_array();
    
    echo $Form->textarea_field('tweetStatus', 'Tweet', (isset($details['tweetStatus']) ? $details['tweetStatus'] : ''), 'xs');
    echo $Form->date_field('tweetSendDate', 'Send on', (isset($details['tweetSendDate']) ? $details['tweetSendDate'] : ''), true);


    if (is_object($Tweet) && $Tweet->tweetSent()=='1') {
        echo $Form->hint('Will cause the tweet to be resent');
        echo $Form->checkbox_field('mark_as_unsent', 'Mark as unsent', '1');
    }


    echo $Form->submit_field();
    echo $Form->form_end();
    
    // Footer
    include (PERCH_PATH.'/core/inc/main_end.php');
