<?php
    # Side panel
    echo $HTML->side_panel_start();

    echo $HTML->para('This page lists your subscribers');


    echo $HTML->side_panel_end();
   
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');
    

	# Title panel
    echo $HTML->heading1('Listing Subscribers');
    
    if (isset($message)) echo $message;
?>

    <?php
    /* ----------------------------------------- SMART BAR ----------------------------------------- */

    ?>
    <ul class="smartbar">
        <li class="selected"><a href="<?php echo $API->app_path() ?>"><?php echo $Lang->get('Subscribers'); ?></a></li>
        <li class="fin"><a class="icon import" href="<?php echo $API->app_path() ?>/import/"><?php echo $Lang->get('Import'); ?></a></li>
        <li class="fin"><a class="icon setting" href="<?php echo $API->app_path() ?>/settings/webhooks/"><?php echo $Lang->get('Web hooks'); ?></a></li>
    </ul>

    <?php
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */


    if (!PerchUtil::count($listmembers)) {
        if ($list_id) $Alert->set('notice', $Lang->get('There are no subscribers yet. Have you imported the list?'));
    }

    echo $Alert->output();

    if (PerchUtil::count($listmembers)) {
?>
    <table class="d">
        <thead>
            <tr>
                <th><?php echo $Lang->get('Email'); ?></th>
                <th><?php echo $Lang->get('Date'); ?></th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($listmembers as $Post) {
?>
            <tr>
                <td class="primary">
                    <a href="<?php echo $HTML->encode($API->app_path()); ?>/subscriber/?id=<?php echo $HTML->encode(urlencode($Post->id())); ?>">
                    <?php echo $HTML->encode($Post->subscriberEmail()); ?></a>
                </td>
                <td>
                <?php echo $HTML->encode(strftime('%d %B %Y, %l:%M %p', strtotime($Post->subscriberDate()))); ?>
                </td>
               
            </tr>

<?php   
    }
?>
        </tbody>
    </table>
<?php    
        if ($Paging->enabled()) {
            echo $HTML->paging($Paging);
        }
    

    } // if pages
    
    echo $HTML->main_panel_end();
?>