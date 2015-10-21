<?php

class PerchMailchimp extends PerchAPI_Factory
{
    protected $table     = 'mailchimp_stats';
	protected $pk        = 'statsID';
	protected $singular_classname = 'PerchMailchimp_Stat';
	
	protected $default_sort_column = 'statID';
	
    
    
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