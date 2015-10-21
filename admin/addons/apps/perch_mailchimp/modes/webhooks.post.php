<?php
    # Side panel
    echo $HTML->side_panel_start();

    echo $HTML->para('Configure webhooks for your list.');

    

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');


    

	# Title panel
    echo $HTML->heading1('Setting up Webhooks');
    
    if (isset($message)) echo $message;
?>

    <?php
    /* ----------------------------------------- SMART BAR ----------------------------------------- */
    
    ?>
    <ul class="smartbar">
        <li class=""><a href="<?php echo $API->app_path() ?>"><?php echo $Lang->get('Subscribers'); ?></a></li>
        <li class="fin"><a class="icon import" href="<?php echo $API->app_path() ?>/import/"><?php echo $Lang->get('Import'); ?></a></li>
        <li class="fin selected"><a class="icon setting" href="<?php echo $API->app_path() ?>/settings/webhooks/"><?php echo $Lang->get('Webhooks'); ?></a></li>
    </ul>

     <?php echo $Alert->output(); ?>
    <?php

    /* ----------------------------------------- /SMART BAR ----------------------------------------- */
    ?>


<?php
    if ($Form->submitted()) {
        echo '<ul class="importables">';
        $MailChimp->set_up_webhooks($api_key, $list_id, true);
        echo '</ul>';

    }else{
        echo $Form->form_start('import', 'magnetic-save-bar');
        echo '<div class="info">';
        echo $HTML->para('Webhooks are how MailChimp lets your site know about changes to your list. When a change occurs, MailChimp contacts your site using the URL given to it when the webhook is set up. Webhooks only work with live sites, so be sure to set them up when making your site live, as well as importing a fresh copy of your list.');
        echo '</div>';
        echo $Form->submit_field('btnSubmit', 'Set up Webhooks with MailChimp', $API->app_path());
        echo $Form->form_end();
    }

    echo $HTML->main_panel_end();
?>