<?php
    
    # Side panel
    echo $HTML->side_panel_start();
    echo $HTML->para('Get a Twitter consumer key and secret by %screating a Twitter app%s. Be sure to include a Callback URL (it\'s not used - your homepage URL will be fine).',
                        '<a href="https://dev.twitter.com/apps">',
                        '</a>');
    echo $HTML->para('Enter your Twitter user name (without the @). To fetch tweets from  multiple Twitter accounts enter the user names separated by a comma.');
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
    include('_subnav.php');


    echo $HTML->heading1('Configuring Twitter');





    if ($message) echo $message;    
    
    echo $HTML->heading2('Twitter Settings');
    
   
    echo $Form->form_start();
    
        echo $Form->text_field('settingTwitterKey', 'Twitter consumer key', $details['settingTwitterKey']);

        echo $Form->text_field('settingTwitterSecret', 'Twitter consumer secret', $details['settingTwitterSecret']);

        echo $Form->text_field('settingTwitterID', 'Your Twitter username', $details['settingTwitterID']);

        $opts = array();
        $opts[] = array('value'=>'0',       'label'=>'Manually');
        $opts[] = array('value'=>'10',      'label'=>'Every 10 minutes');
        $opts[] = array('value'=>'15',      'label'=>'Every 15 minutes');
        $opts[] = array('value'=>'30',      'label'=>'Every 30 minutes');
        $opts[] = array('value'=>'60',      'label'=>'Every hour');
        $opts[] = array('value'=>'120',     'label'=>'Every 2 hours');
        $opts[] = array('value'=>'240',     'label'=>'Every 4 hours');
        $opts[] = array('value'=>'360',     'label'=>'Every 6 hours');
        $opts[] = array('value'=>'720',     'label'=>'Every 12 hours');
        $opts[] = array('value'=>'1440',    'label'=>'Every day');
        $opts[] = array('value'=>'10080',   'label'=>'Every week');
        $opts[] = array('value'=>'40320',   'label'=>'Every month');


        echo $Form->select_field('settingUpdateInterval', 'Check for updates', $opts, $details['settingUpdateInterval']);
        echo $Form->checkbox_field('reauth', 'Force reauthentication', '1', '0');

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    
    echo $Form->form_end();
    echo $HTML->main_panel_end();

	
?>