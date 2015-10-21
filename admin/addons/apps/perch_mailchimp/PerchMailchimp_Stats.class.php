<?php

class PerchMailchimp_Stats extends PerchAPI_Factory
{
    protected $table     = 'mailchimp_stats';
	protected $pk        = 'statsID';
	protected $singular_classname = 'PerchMailchimp_Stat';
	
	protected $default_sort_column = 'statID';
	
    /**
    *
    */
    public function populate($api_key, $list_id, $echo_feedback=false)
    {
    	$MailChimp = new MailChimp($api_key);

    	if ($echo_feedback) {
            $API  = new PerchAPI(1.0, 'perch_mailchimp');
            $Lang = $API->get('Lang');
        }

        $opts = array(
            'apikey'=>$api_key,
            'filters'=>array(
                'list_id'=>$list_id,
            )
        );

        $result = $MailChimp->call('lists/list',$opts);

        if($result) {
        	$this->db->execute('TRUNCATE TABLE '.$this->table);
	    	//store title in data array
	    	$stats_array = array(
	    	  'title'=> $result['data'][0]['name'],
	    	  'total' => $result['data'][0]['stats']['member_count']
	    	);

	    	$list_opts = array(
            'apikey'=>$api_key,
            'id'=>$list_id
        	);

	    	$activity = $MailChimp->call('lists/activity',$list_opts);
	    	PerchUtil::debug($activity);
	    	foreach($activity as $stat) {
		    	if($stat['day'] == date('Y-m-d', strtotime('-1 days'))) {
		    		$stats_array['yesterday'] = $stat['subs']+$stat['other_adds'];
		    	}elseif($stat['day'] == date('Y-m-d')) {
		    		$stats_array['today'] = $stat['subs']+$stat['other_adds'];
		    	}
		    }

		    //insert stats array
	    	$this->db->insert($this->table, $stats_array);

	    	if ($echo_feedback) {
                echo '<li class="icon success">';
                echo $Lang->get('Importing statistics for list %s', $list_id);
                echo '</li>';
                flush();
            }

	    	// history table

	    	$sql = 'SELECT * FROM '.PERCH_DB_PREFIX .'mailchimp_history WHERE historyDate = '.$this->db->pdb(date('Y-m-d', strtotime('-1 days'))) . ' LIMIT 1';
			if(!$row = $this->db->get_row($sql)) {
				//insert a row for yesterday
				$history_data = array(
					'historyDate'=>date('Y-m-d', strtotime('-1 days')),
					'historyTotal'=>$stats_array['yesterday']
				);
				$this->db->insert(PERCH_DB_PREFIX .'mailchimp_history',$history_data);

				if ($echo_feedback) {
	                echo '<li class="icon success">';
	                echo $Lang->get('Importing history for list %s', $list_id);
	                echo '</li>';
	                flush();
	            }
			}
        }

        return true;
    }
    
    /**
     * get the stored data for display on the dashboard
     */
    public function get_data()
    {
		//get the stats
        $sql = 'SELECT * FROM '.$this->table .' LIMIT 1';

        $row = $this->db->get_row($sql);
        if($row) {
	        //get the subscribers
	        $sql = 'SELECT * FROM '.PERCH_DB_PREFIX .'mailchimp_subscribers ORDER BY subscriberDate DESC LIMIT 5';
	        $rows = $this->db->get_rows($sql);
	        


	        $row['subscribers'] = $rows;


	              
	        return $this->return_instance($row);
        }
        return false;
    }

    
 
    
   
	
}
?>