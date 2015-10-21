<?php
    
    # Side panel
    echo $HTML->side_panel_start();
    echo $HTML->para('Schedule tweets to be sent at a future date and time.');
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
    include('_subnav.php');

    echo '<a class="add button" href="' . PerchUtil::html($API->app_path().'/scheduled/edit/').'">'.PerchLang::get('Add tweet').'</a>';



    echo $HTML->heading1('Scheduled Tweets');

    /* ----------------------------------------- SMART BAR ----------------------------------------- */

    echo $HTML->smartbar(
            $HTML->smartbar_link(($state=='unsent'), 
                    array( 
                        'link' => '?filter=unsent',
                        'label' => PerchLang::get('Scheduled'),
                    )
                ),
            $HTML->smartbar_link(($state=='sent'), 
	                array( 
	                    'link' => '?filter=sent',
	                    'label' => PerchLang::get('Sent'),
	                )
               	)
        );


    /* ---------------------------------------- /SMART BAR ----------------------------------------- */


    
    echo $HTML->listing($tweets, 
    		array('Date', 'Tweet'), 
    		array('tweetSendDate', 'tweetStatus'), 
            array(
                    'edit' => 'edit',
                    'delete' => 'delete',
                ),
            array(
                'user' => $CurrentUser,
                'edit' => 'perch_twitter.schedule',
                'delete' => 'perch_twitter.schedule',
                )
            );



    echo $HTML->main_panel_end();

