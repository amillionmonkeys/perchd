<?php
    # Side panel
    echo $HTML->side_panel_start();

    echo $HTML->para('Perform a mass import of your list.');

    

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');


    

	# Title panel
    echo $HTML->heading1('Importing List Data');
    
    if (isset($message)) echo $message;
?>

    <?php
    /* ----------------------------------------- SMART BAR ----------------------------------------- */
    
    ?>
    <ul class="smartbar">
        <li class=""><a href="<?php echo $API->app_path() ?>"><?php echo $Lang->get('Subscribers'); ?></a></li>
        <li class="fin selected"><a class="icon import" href="<?php echo $API->app_path() ?>/import/"><?php echo $Lang->get('Import'); ?></a></li>
        <li class="fin"><a class="icon setting" href="<?php echo $API->app_path() ?>/settings/webhooks/"><?php echo $Lang->get('Webhooks'); ?></a></li>
    </ul>

     <?php echo $Alert->output(); ?>


    <?php

    /* ----------------------------------------- /SMART BAR ----------------------------------------- */
    ?>
<?php

    if ($Form->submitted()) {
        echo '<ul class="importables">';
        $MailChimp->import_list($api_key, $list_id, true);
        $MailChimp->get_campaigns($api_key, $list_id, true);
        $MailChimp->get_stats_data($api_key, $list_id, true);
        echo '<li class="icon success">'.$Lang->get('Done.').'</li>';
        echo '</ul>';

    }else{
        echo $Form->form_start('import', 'magnetic-save-bar');
        echo '<div class="info">';
        echo $HTML->para('You can import your list from MailChimp, and after that it is kept up to date using Webhooks.');
        echo $HTML->para('Importing completely refreshes any existing data, pulling it down from MailChimp and replacing what was already there. It\'s essential to do this if your site has running without receiving webhooks, as your copy of the list could be out of date.');
        echo '</div>';
        echo $Form->submit_field('btnSubmit', 'Import from MailChimp', $API->app_path());
        echo $Form->form_end();

    }



    
    echo $HTML->main_panel_end();
?>