<?php
    # Side panel
    echo $HTML->side_panel_start();

    echo $HTML->para('Subscriber detail');

    

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');


    

	# Title panel
    echo $HTML->heading1('Subscriber detail');
    
    if (isset($message)) echo $message;
?>

    <?php echo $HTML->heading2('Subscriber'); ?>

    <table class="factsheet">
        <tr>
            <th><?php echo $Lang->get('Email address'); ?></th>
            <td><?php echo $details['subscriberEmail'];?></td>
        </tr>
        <tr>
            <th><?php echo $Lang->get('Join date'); ?></th>
            <td><?php echo $details['subscriberDate'];?></td>
        </tr>
    </table>



<?php
    
    echo $HTML->main_panel_end();
?>