<?php
	include('PerchTwitter_Tweets.class.php');
	include('PerchTwitter_Tweet.class.php');
	

    $API   = new PerchAPI(1.0, 'perch_twitter');
    $Lang  = $API->get('Lang');
    $Tweets = new PerchTwitter_Tweets($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(5);

    $tweets = $Tweets->get_all($Paging);

?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Twitter'); ?>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($tweets)) {
				echo '<ul>';
				foreach($tweets as $Tweet) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_twitter/').'">';
							echo PerchUtil::html($Tweet->tweetText());
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>
</div>