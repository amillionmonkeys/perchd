<?php
    # Side panel
    echo $HTML->side_panel_start();

    echo $HTML->para('This page lists your sent campaigns');

    

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');


    

	# Title panel
    echo $HTML->heading1('Listing Campaigns');
    
    if (isset($message)) echo $message;

    if (!PerchUtil::count($list)) {
        $Alert->set('notice', $Lang->get('There are no campaigns yet.'));
    }

    echo $Alert->output(); 


    if (PerchUtil::count($list)) {
?>
    <table class="">
        <thead>
            <tr>
                <th class="first"><?php echo $Lang->get('Name'); ?></th>
                <th><?php echo $Lang->get('Subject'); ?></th>
                <th><?php echo $Lang->get('Date sent'); ?></th>
                <th><?php echo $Lang->get('No. emails sent'); ?></th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($list as $Post) {
?>
            <tr>
                <td class="primary">
                    <a href="<?php echo $HTML->encode($API->app_path()); ?>/campaigns/view/?id=<?php echo $HTML->encode(urlencode($Post->id())); ?>">
                    <?php echo $HTML->encode($Post->campaignTitle()); ?></a>
                </td>
                <td>
                    <?php echo $HTML->encode($Post->campaignSubject()); ?>
                </td>
                <td>
                <?php echo $HTML->encode(strftime('%d %B %Y, %l:%M %p', strtotime($Post->campaignSendTime()))); ?>
                </td>
               <td>
                    <?php echo $HTML->encode($Post->campaignSent()); ?>
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