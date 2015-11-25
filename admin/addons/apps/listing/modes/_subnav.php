<?php

	$Listing = new Listings($API);
	$pending_comment_count =$Listing->get_count('PENDING');

	echo $HTML->subnav($CurrentUser, array(
		array('page'=>array(
					'listing',
					'listing/edit'
			), 'label'=>'Moderate', 'priv'=>'listing.moderate'),
	));
?>