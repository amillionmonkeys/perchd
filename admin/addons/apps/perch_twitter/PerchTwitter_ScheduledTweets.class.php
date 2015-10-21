<?php

class PerchTwitter_ScheduledTweets extends PerchAPI_Factory
{
    protected $table     = 'twitter_scheduled_tweets';
	protected $pk        = 'tweetID';
	protected $singular_classname = 'PerchTwitter_ScheduledTweet';
	
	protected $default_sort_column = 'tweetSendDate';
    
       
	public function all_unsent($Paging=false)
	{
	    if ($Paging && $Paging->enabled()) {
	        $sql = $Paging->select_sql();
	    }else{
	        $sql = 'SELECT';
	    }
	    
	    $sql .= ' * 
	            FROM ' . $this->table. ' WHERE tweetSent=0 ';
	            
	    if (isset($this->default_sort_column)) {
	        $sql .= ' ORDER BY ' . $this->default_sort_column . ' '.$this->default_sort_direction;
	    }
	    
	    if ($Paging && $Paging->enabled()) {
	        $sql .=  ' '.$Paging->limit_sql();
	    }
	    
	    $results = $this->db->get_rows($sql);
	    
	    if ($Paging && $Paging->enabled()) {
	        $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
	    }
	    
	    return $this->return_instances($results);
	}

	public function all_sent($Paging=false)
	{
	    if ($Paging && $Paging->enabled()) {
	        $sql = $Paging->select_sql();
	    }else{
	        $sql = 'SELECT';
	    }
	    
	    $sql .= ' * 
	            FROM ' . $this->table. ' WHERE tweetSent=1 ';
	            
	    if (isset($this->default_sort_column)) {
	        $sql .= ' ORDER BY ' . $this->default_sort_column . ' DESC';
	    }
	    
	    if ($Paging && $Paging->enabled()) {
	        $sql .=  ' '.$Paging->limit_sql();
	    }
	    
	    $results = $this->db->get_rows($sql);
	    
	    if ($Paging && $Paging->enabled()) {
	        $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
	    }
	    
	    return $this->return_instances($results);
	}
}
