<?php
	include('MailChimp.class.php');
	include('PerchMailchimp_Stats.class.php');
	include('PerchMailchimp_Stat.class.php');

    $API   = new PerchAPI(1.0, 'perch_mailchimp');
    $Lang  = $API->get('Lang');

    // check privs
    if($CurrentUser->has_priv('perch_mailchimp')) {

	    $Settings = $API->get('Settings');
	    $api_key = $Settings->get('perch_mailchimp_api_key')->settingValue();
	    $list_id = $Settings->get('perch_mailchimp_list_id')->settingValue();
	    
	    $Data = new PerchMailchimp_Stats($API);
	    
	    $msg = false;
	    if(!$api_key || $api_key == '' || !$list_id || $list_id == '') {
	    	//need to set these
	    	$msg = '<p class="bd helptext"><a href="'.PERCH_LOGINPATH.'/core/settings/">'. PerchLang::get('You must set your Mailchimp API Key and List ID in Settings.').'</a></p>';
	    }else{    	
	    	$display_data = $Data->get_data();
	    }
	   

	?>

	<div class="widget">
		<h2><?php echo $Lang->get('MailChimp'); ?></h2>
		<div class="">
			<?php
			if ($msg) {
				echo $msg;
			} else {
				if(is_object($display_data)) {
					echo '<table><caption>'.PerchUtil::html($display_data->title()).'</caption>'."\n";
					
					echo '<tr><th>'.$Lang->get('Total subscribers').'</th><td class="total">'.PerchUtil::html($display_data->total()).'</td></tr>'."\n";
					echo '<tr><th>'.$Lang->get('Yesterday').'</th><td class="total">'.PerchUtil::html($display_data->yesterday()).'</td></tr>'."\n";
					echo '<tr><th>'.$Lang->get('Today').'</th><td class="total">'.PerchUtil::html($display_data->today()).'</td></tr>'."\n";
					echo '</table>'."\n";
					
					$subs = $display_data->subscribers();
						
					if(is_array($subs) && PerchUtil::count($subs)>0) {
						echo '<table><caption>'.$Lang->get('Latest subscribers').'</caption>'."\n";
							
						foreach($subs as $sub_row) {
							echo '<tr><td>'.PerchUtil::html($sub_row['subscriberEmail']).'</td><td class="note">'.PerchUtil::html(strftime('%d %b %Y', strtotime($sub_row['subscriberDate']))).'</td></tr>'."\n";
						}
						echo '</table>'."\n";
					}
				}
				
			}
			?>
		</div>
	</div>
<?php } ?>