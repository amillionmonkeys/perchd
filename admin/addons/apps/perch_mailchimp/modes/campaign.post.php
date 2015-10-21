<?php
    # Side panel
    echo $HTML->side_panel_start();

    echo $HTML->para('Campaign information');

    

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');


    

	# Title panel
    echo $HTML->heading1('Campaign information');
    
    if (isset($message)) echo $message;
?>
    <?php echo $HTML->heading2('Campaign: %s', $Campaign->campaignTitle()); ?>

    <table class="factsheet">

        <tr>
            <th><?php echo $Lang->get('Send time'); ?></th>
            <td><?php echo $HTML->encode($Campaign->campaignSendTime()); ?></td>
        </tr>
        <tr>
            <th><?php echo $Lang->get('Emails sent'); ?></th>
            <td><?php echo $HTML->encode($Campaign->campaignSent()); ?></td>
        </tr>
        <tr>
            <th><?php echo $Lang->get('Subject line'); ?></th>
            <td><?php echo $HTML->encode($Campaign->campaignSubject()); ?></td>
        </tr>
        <tr>
            <th><?php echo $Lang->get('Archive URL'); ?></th>
            <td>
                <a href="<?php echo $HTML->encode($Campaign->campaignArchiveURL()); ?>"><?php echo $HTML->encode($Campaign->campaignArchiveURL()); ?></a>
            </td>
        </tr>

    </table>


<iframe src="<?php echo $HTML->encode($API->app_path()); ?>/campaigns/view/html/?id=<?php echo $HTML->encode(urlencode($details['campaignID'])); ?>" name="html-version" style="width:100%; height:600px;border:1px solid #999; margin-top: 30px;"></iframe>

<?php
    
    echo $HTML->main_panel_end();
?>