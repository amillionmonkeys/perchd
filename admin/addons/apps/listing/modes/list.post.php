<?php
   
    # Side panel
    echo $HTML->side_panel_start();
    
    echo $HTML->para('This page lists listings that have been submitted by website visitors.');
    echo $HTML->para('The Pending category lists listings that are waiting approval to be published on the site.');
   
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();

	include ('_subnav.php');
	
	# Title panel
    echo $HTML->heading1('Listings');
    
    
    if (isset($message)) echo $message;


    
    /* ----------------------------------------- SMART BAR ----------------------------------------- */
    ?>


    <ul class="smartbar">
        <li class="<?php echo ($status=='all'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path().'/?status=all'); ?>"><?php echo $Lang->get('All'); ?></a></li>
        <li class="new <?php echo ($status=='pending'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path().'/'.'?status=pending'); ?>"><?php echo $Lang->get('Pending (%s)', $pending_listing_count); ?></a></li>
        <?php

            if ($status == 'pending') {
                $Alert->set('filter', $Lang->get('You are viewing all listings pending moderation.'). ' <a href="'.$API->app_path().'/?status=all'.'" class="action">'.$Lang->get('Clear Filter').'</a>');
            }
        
            $items = array();

            $items[] = array(
                    'arg'=>'status',
                    'val'=>'live',
                    'label'=>'Live',
                    'path'=>$API->app_path().'/'
                );
            $items[] = array(
                    'arg'=>'status',
                    'val'=>'rejected',
                    'label'=>'Rejected',
                    'path'=>$API->app_path().'/'
                );
            $items[] = array(
                    'arg'=>'status',
                    'val'=>'spam',
                    'label'=>'Spam',
                    'path'=>$API->app_path().'/'
                );
            
           
            echo PerchUtil::smartbar_filter('cf', 'By Status', 'Filtered by ‘%s’', $items, 'folder', $Alert, "You are viewing ‘%s’ listings", $API->app_path().'/?status=all');
        
        ?>
    </ul>

     <?php echo $Alert->output(); ?>


    <?php
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */


    
    if (PerchUtil::count($listings)) {
	
		echo $Form->form_start('listings', 'bulk-edit');
?>
    <table class="d">
        <thead>
            <tr>
                <th><?php echo $Lang->get('Date'); ?></th>
				<th><?php echo $Lang->get('Title'); ?></th>
				<th><?php echo $Lang->get('Author'); ?></th>
                <th><?php echo $Lang->get('Status'); ?></th>
			
            </tr>
        </thead>
        <tbody>
<?php
    foreach($listings as $Listing) {
?>
            <tr>
                <td class="primary">
                    <?php echo $Form->checkbox('listing-'.$Listing->id(), '1', 0); ?>
                    <a href="<?php echo $HTML->encode($API->app_path()); ?>/edit/?id=<?php echo $HTML->encode(urlencode($Listing->id())); ?>" class="edit">
                    <?php echo $HTML->encode($Listing->listingTitle()); ?>
                    </a>
                </td>
                <td><?php echo strftime('%d&nbsp;%h&nbsp;%y', strtotime($Listing->listingDateTime())); ?></td>

                <td><?php echo $HTML->encode($Listing->memberID()); ?></td> 
                <td><?php echo $HTML->encode($Listing->listingStatus()); ?></td>             
                
            </tr>

<?php   
    }
?>
        </tbody>
    </table>
    <div class="controls">
<?php    
		$opts = array();
		$opts[] = array('label'=>'', 'value'=>'');
		$opts[] = array('label'=>'Live', 'value'=>'LIVE');
		$opts[] = array('label'=>'Rejected', 'value'=>'REJECTED');
		$opts[] = array('label'=>'Pending', 'value'=>'PENDING');
		    		
		echo $Form->select_field('listingStatus', 'Mark selected as', $opts);
        echo $Form->submit_field('btnSubmit', 'Save');


?>
    </div>
<?php    
        if ($Paging->enabled()) {
            echo $HTML->paging($Paging);
        }

    echo $Form->form_end();
    

    } // if pages
    
    echo $HTML->main_panel_end();
?>