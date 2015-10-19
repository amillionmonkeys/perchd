<?php
	include('PerchComments_Comments.class.php');
    include('PerchComments_Comment.class.php');

    $API   = new PerchAPI(1, 'perch_comments');
    $Lang  = $API->get('Lang');

    $Comments = new PerchComments_Comments($API);
    
    $comment_count = $Comments->get_count();

    $comments = array();
    $comments['Pending'] = $Comments->get_count('PENDING');
    $comments['Live'] = $Comments->get_count('LIVE');
    $comments['Rejected'] = $Comments->get_count('REJECTED');
    $comments['Spam'] = $Comments->get_count('SPAM');
?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Comments'); ?> <span class="note"><?php echo PerchUtil::html($comment_count); ?></span>
	</h2>
	<div class="bd">
		<?php 
			echo '<ul class="mod">';
				foreach($comments as $label=>$count) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_comments/?status='.strtolower($label)).'">';
							echo PerchUtil::html($Lang->get($label). ' ('.$count.')');
						echo '</a>';
					echo '</li>';
				}
			echo '</ul>';
		?>
	</div>
</div>