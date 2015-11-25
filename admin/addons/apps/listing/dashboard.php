<?php
	include('Listings.class.php');
    include('Listing.class.php');

    $API   = new PerchAPI(1, 'listing');
    $Lang  = $API->get('Lang');

    $Listings = new Listing($API);

    $listing_count = $Listings->get_count();
    
    $directories = array();
    $directories['Pending'] = $Listings->get_count('PENDING');
    $directories['Live'] = $Listings->get_count('LIVE');
    $directories['Rejected'] = $Listings->get_count('REJECTED');
?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Listing'); ?> <span class="note"><?php echo PerchUtil::html($comment_count); ?></span>
	</h2>
	<div class="bd">
		<?php 
			echo '<ul class="mod">';
				foreach($listing as $label=>$count) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/listing/?status='.strtolower($label)).'">';
							echo PerchUtil::html($Lang->get($label). ' ('.$count.')');
						echo '</a>';
					echo '</li>';
				}
			echo '</ul>';
		?>
	</div>
</div>