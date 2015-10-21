<?php

class PerchMailchimp_Util extends PerchAPI_Factory
{
    protected $table     = 'mailchimp_stats';
	protected $pk        = 'statsID';
	protected $singular_classname = 'PerchMailchimp_Stat';
	
	protected $default_sort_column = 'statID';
	      
     /**
     * Clear subscriber list and reimport from MailChimp.
     * Uses the export API to get initial data
     * @param  [type]  $api_key       [description]
     * @param  [type]  $list_id       [description]
     * @param  boolean $echo_feedback [description]
     * @return [type]                 [description]
     */
	public function import_list($api_key, $list_id, $echo_feedback=true)
	{
		$Settings = $this->api->get('Settings');

		if ($echo_feedback) {
			flush();
			$API  = new PerchAPI(1.0, 'perch_mailchimp');
   			$Lang = $API->get('Lang');
		}


		//get subscribers
		$chunk_size = 4096; //in bytes
		$url = 'http://<dc>.api.mailchimp.com/export/1.0/list?apikey='.$api_key.'&id='.$list_id;

		list(, $datacentre) = explode('-', $api_key);
        $url = str_replace('<dc>', $datacentre, $url);
       		
		$handle = @fopen($url,'r');
		
		if (!$handle) {
			if ($echo_feedback) {   		
	    		echo '<li class="icon failure">';
	        	echo $Lang->get('Could not access MailChimp API.');
	        	echo '</li>';
	        	flush();
	    	}else{
	    		return false;
	    	}

		}else{
			

			$this->db->execute('TRUNCATE TABLE '.PERCH_DB_PREFIX .'mailchimp_subscribers');
		  	
		  	$i = 0;
		  	$header = array();
		  	$subs = array();
		  
		  	while (!feof($handle)) {
		    	$buffer = fgets($handle, $chunk_size);
		    	if (trim($buffer)!=''){
		      		$obj = json_decode($buffer);
		      		if ($i==0){
		        		//store the header row
		        		$header = $obj;
		      		}else{
		        		$item = array();
		        		$count = PerchUtil::count($obj);
		        		if($count>0) {
		        			for($n=0;$n<$count;$n++) {
		        	  			$item[$header[$n]] = $obj[$n];
		        			}
		        		}
		        		$subs_array = array(
		    				'subscriberDate'=>$item['CONFIRM_TIME'],
		    				'subscriberEmail'=>$obj[0]
		    			);
		    			$this->db->insert(PERCH_DB_PREFIX .'mailchimp_subscribers',$subs_array);

				    	if ($echo_feedback) {
				    		echo '<li class="icon success">';
		                	echo $Lang->get('Importing %s', $obj[0]);
		                	echo '</li>';
		                	flush();
				    	}
				        $subs[] = $item;
					}
		      		$i++;
		    	}
		  	}

		  	fclose($handle);
		}
	}

	public function get_campaigns($api_key, $list_id, $echo_feedback=true)
	{
		if ($echo_feedback) {
			$API  = new PerchAPI(1.0, 'perch_mailchimp');
    		$Lang = $API->get('Lang');
    		
    		echo '<li class="icon success">';
        	echo $Lang->get('Importing campaigns...');
        	echo '</li>';
        	flush();
    	}


		// get campaign data
		$Campaigns = new PerchMailchimp_Campaigns();
		$Campaigns->populate($api_key, $list_id, $echo_feedback);



	}

	public function get_stats_data($api_key, $list_id, $echo_feedback=true)
	{

		if ($echo_feedback) {
			$API  = new PerchAPI(1.0, 'perch_mailchimp');
    		$Lang = $API->get('Lang');
    		
    		echo '<li class="icon success">';
        	echo $Lang->get('Importing statistics...');
        	echo '</li>';
        	flush();
    	}


		// get stats data
		$Stats = new PerchMailchimp_Stats();
		$Stats->populate($api_key, $list_id, $echo_feedback);

	}
    
    public function set_up_webhooks($api_key, $list_id, $echo_feedback=true)
    {
    	if ($echo_feedback) {
			$API  = new PerchAPI(1.0, 'perch_mailchimp');
    		$Lang = $API->get('Lang');
        	flush();
    	}

    	$Settings = $this->api->get('Settings');

    	// set up webhooks
		$secret = $Settings->get('perch_mailchimp_secret')->settingValue();
		$MailChimp = new MailChimp($api_key);
		$opts = array(
			'apikey'=>$api_key,
			'id'=>$list_id,
			'url'=>'http://'.getenv('HTTP_HOST').PERCH_LOGINPATH.'/addons/apps/perch_mailchimp/webhooks/subscribe.php?perch='.$secret,
			'actions'=>array(
				'subscribe'=>true,
				'unsubscribe'=>false,
				'profile'=>false,
				'cleaned'=>false,
				'upemail'=>false,
				'campaign'=>false
				)
			);

		$result = $MailChimp->call('lists/webhook-add',$opts);

    	if ($echo_feedback) {
			if ($result && isset($result['error'])) {
				echo '<li class="icon failure">';
        		echo $Lang->get('Error setting up Subscribe webhook &mdash; %s', $result['error']);
        		echo '</li>';
			}else{
				echo '<li class="icon success">';
        		echo $Lang->get('Setting up Subscribe webhook &mdash; done.');
        		echo '</li>';
			}  		
        	flush();
    	}


		$opts = array(
			'apikey'=>$api_key,
			'id'=>$list_id,
			'url'=>'http://'.getenv('HTTP_HOST').PERCH_LOGINPATH.'/addons/apps/perch_mailchimp/webhooks/unsubscribe.php?perch='.$secret,
			'actions'=>array(
				'subscribe'=>false,
				'unsubscribe'=>true,
				'profile'=>false,
				'cleaned'=>true,
				'upemail'=>false,
				'campaign'=>false
				)
			);

		$result = $MailChimp->call('lists/webhook-add',$opts);

    	if ($echo_feedback) {
			if ($result && isset($result['error'])) {
				echo '<li class="icon failure">';
        		echo $Lang->get('Error setting up Unsubscribe webhook &mdash; %s', $result['error']);
        		echo '</li>';
			}else{
				echo '<li class="icon success">';
        		echo $Lang->get('Setting up Unsubscribe webhook &mdash; done.');
        		echo '</li>';
			}  		
        	flush();
    	}


		$opts = array(
			'apikey'=>$api_key,
			'id'=>$list_id,
			'url'=>'http://'.getenv('HTTP_HOST').PERCH_LOGINPATH.'/addons/apps/perch_mailchimp/webhooks/upemail.php?perch='.$secret,
			'actions'=>array(
				'subscribe'=>false,
				'unsubscribe'=>false,
				'profile'=>false,
				'cleaned'=>false,
				'upemail'=>true,
				'campaign'=>false
				)
			);

		$result = $MailChimp->call('lists/webhook-add',$opts);
		
		if ($echo_feedback) {
			if ($result && isset($result['error'])) {
				echo '<li class="icon failure">';
        		echo $Lang->get('Error setting up Email Address Change webhook &mdash; %s', $result['error']);
        		echo '</li>';
			}else{
				echo '<li class="icon success">';
        		echo $Lang->get('Setting up Email Address Change webhook &mdash; done.');
        		echo '</li>';
			}  		
        	flush();
    	}

		$opts = array(
			'apikey'=>$api_key,
			'id'=>$list_id,
			'url'=>'http://'.getenv('HTTP_HOST').PERCH_LOGINPATH.'/addons/apps/perch_mailchimp/webhooks/campaign.php?perch='.$secret,
			'actions'=>array(
				'subscribe'=>false,
				'unsubscribe'=>false,
				'profile'=>false,
				'cleaned'=>false,
				'upemail'=>false,
				'campaign'=>true
				)
			);

		$result = $MailChimp->call('lists/webhook-add',$opts);

    	if ($echo_feedback) {
			if ($result && isset($result['error'])) {
				echo '<li class="icon failure">';
        		echo $Lang->get('Error setting up Campaign webhook &mdash; %s', $result['error']);
        		echo '</li>';
			}else{
				echo '<li class="icon success">';
        		echo $Lang->get('Setting up Campaign webhook &mdash; done.');
        		echo '</li>';
			}  		
        	flush();
    	}
    }
}
?>