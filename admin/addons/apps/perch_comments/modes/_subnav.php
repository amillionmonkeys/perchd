<?php

	$Comments = new PerchComments_Comments($API);
	$pending_comment_count =$Comments->get_count('PENDING');

	echo $HTML->subnav($CurrentUser, array(
		array('page'=>array(
					'perch_comments',
					'perch_comments/edit'
			), 'label'=>'Moderate', 'priv'=>'perch_comments.moderate'),
	));
?>