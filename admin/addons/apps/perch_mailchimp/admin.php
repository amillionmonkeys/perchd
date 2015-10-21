<?php
	if (!function_exists('json_decode')) {
        die('This app requires the PHP JSON library, which is not available on your server. Your host should be able to enable it for you.');
    }

   	if ($CurrentUser->logged_in()) {
   		$this->register_app('perch_mailchimp', 'MailChimp', 1, 'MailChimp', '2.0.1');
    	$this->require_version('perch_mailchimp', '2.3.4');
    	$this->add_setting('perch_mailchimp_api_key', 'MailChimp API Key', 'text', '');
    	$this->add_setting('perch_mailchimp_list_id', 'MailChimp List ID', 'text', '');
      $this->add_setting('perch_mailchimp_campaign_url', 'Campaign archive page path', 'text', '/mailchimp/campaign.php?s={campaignSlug}');
	}
?>