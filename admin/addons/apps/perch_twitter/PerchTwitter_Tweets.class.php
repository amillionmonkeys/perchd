<?php

class PerchTwitter_Tweets extends PerchAPI_Factory
{
    protected $table     = 'twitter_tweets';
	protected $pk        = 'tweetID';
	protected $singular_classname = 'PerchTwitter_Tweet';
	
	protected $default_sort_column = 'tweetID';
    
	/**
	 * 
	 * function to get listing of tweets
	 * @param string $twitter_id must be an account already set up in admin
	 * @param string $type favorites|mine
	 * @param array $opts ()
	 */
	public function get_custom($twitter_id=false, $type='mine', $opts=array())
    {
    	
    	if(isset($type) && $type == 'favorites') {
    		$type = 'favorites';
    	}else{
    		$type = 'mine';
    	}
    	
    	if (isset($opts['order'])) {
    		$order = $opts['order'];
    	}else{
    		$order = 'tweetDate DESC'; 
    	}
    	
    	if (isset($opts['exclude_replies']) && $opts['exclude_replies'] == 1) {
    		$exclude_replies = true;
    	}else{
    		$exclude_replies = false;
    	}
    	
    	if (isset($opts['count']) && $opts['count'] != false) {
    		$limit = ' LIMIT '.$opts['count'];
    	}else{
    		$limit = false; 
    	}
    	
    	$sql = 'SELECT * 
                FROM '.PERCH_DB_PREFIX.'twitter_tweets 
                WHERE tweetType='.$this->db->pdb($type);
    	if($twitter_id) {
    		$sql.= ' AND tweetAccount='.$this->db->pdb($twitter_id) ;
    	}
    	if($exclude_replies) {
    		$sql.= ' AND tweetIsReply=0';
    	}
        $sql.=  ' ORDER BY '.$order . $limit;
        $rows    = $this->db->get_rows($sql);
        
        if (isset($opts['link_urls']) && $opts['link_urls']==true) {
            
            if (PerchUtil::count($rows)) {
                foreach($rows as &$row) {
                    $row['tweetText'] = $this->_htmlize_tweet($row['tweetText']); 
                }
            }
            
        }

        // Timezone
        if (class_exists('DateTimeZone') && defined('PERCH_TZ')) {
            $UserTZ = new DateTimeZone(PERCH_TZ);
            $NativeTZ = new DateTimeZone('UTC');

            if (PerchUtil::count($rows)) {
                foreach($rows as &$row) {
                    $Date = new DateTime($row['tweetDate'], $NativeTZ);
                    $Date->setTimezone($UserTZ);
                    $row['tweetDate'] = $Date->format('Y-m-d H:i:s');
                }
            }
        }

        


        
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            return $rows; 
	    }
	    
	    // template
	    if (isset($opts['template'])) {
	        $template = $opts['template'];
	    }else{
	        $template = 'twitter/tweet.html';
	    }
	    
	    $Template = $this->api->get("Template");
	    $Template->set($template, 'twitter');
	    
        $html = $Template->render_group($rows, true);
	    

	    return $html;
    }
    
    /**
     * 
     * Pulls back all tweets regardless of user or type from the table
     * @param int $limit
     */
	public function get_all($Paging=false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }


        $sql .= ' * FROM '.PERCH_DB_PREFIX.'twitter_tweets
                ORDER BY tweetDate DESC';

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }


        $results = $this->db->get_rows($sql);
        
        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        // monkey patch
        if (is_array($results)) {
            if (!array_key_exists('tweetHTML', $results[0])) {
                $sql = 'ALTER TABLE `'.PERCH_DB_PREFIX.'twitter_tweets` ADD `tweetHTML` TEXT AFTER `tweetText`';
                $this->db->execute($sql);
            }
        }


        
        return $this->return_instances($results);
    }



    /**
     * 
     * Pulls back all tweets regardless of user or type from the table
     * @param int $limit
     */
    public function get_all_by_type($type='mine', $Paging=false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }


        $sql .= ' * FROM '.PERCH_DB_PREFIX.'twitter_tweets
                WHERE tweetType='.$this->db->pdb($type).'
                ORDER BY tweetDate DESC';

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }


        $results = $this->db->get_rows($sql);
        
        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }
        
        return $this->return_instances($results);
    }
    
	public function get_tweets($user,$type,$limit=false)
    {
        $sql = 'SELECT * 
                FROM '.PERCH_DB_PREFIX.'twitter_tweets 
                WHERE tweetAccount = '.$this->db->pdb($user).' AND tweetType =  '.$this->db->pdb($type).'
                ORDER BY tweetDate DESC ';
        if($limit) {
        	$sql.= ' LIMIT '.$limit;
        }

        $rows   = $this->db->get_rows($sql);
        
        return $this->return_instances($rows);
    }
    
	public function get_tweet_ids($user,$type)
    {
        $sql = 'SELECT tweetTwitterID 
                FROM '.PERCH_DB_PREFIX.'twitter_tweets 
                WHERE tweetAccount = '.$this->db->pdb($user).' AND tweetType =  '.$this->db->pdb($type);
        

        $rows   = $this->db->get_rows($sql);
        $a = array();
        if(is_array($rows)) {
        	foreach($rows as $row) {
        		$a[] = $row['tweetTwitterID'];
        	}
        }
        
        return $a;
    }
    
    public function get_scheduled_tweets()
    {
        $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'twitter_scheduled_tweets 
                WHERE tweetSent=0 AND tweetSendDate<='.$this->db->pdb(date('Y-m-d H:i:s'));
        return $this->db->get_rows($sql);
    }

    public function mark_scheduled_as_sent($tweetID)
    {
        $data = array(
            'tweetSent' => '1',
            );
        $this->db->update(PERCH_DB_PREFIX.'twitter_scheduled_tweets', $data, 'tweetID', $tweetID);
    }


    /**
     * Create HTML version of tweet (with links etc)
     *
     * @param string $text 
     * @return void
     * @author Drew McLellan
     */
    private function _htmlize_tweet($text)
    {
        //$text = PerchUtil::html($text);
        //$text = $this->_auto_link_text($text); 
        //$text = $this->_link_usernames($text);
        //$text = $this->_link_hashtags($text);
        
        return $text;
    }
    
    
    /**
     * Process tweet to turn @usernames into links
     *
     * @param string $text 
     * @return void
     * @author Drew McLellan
     */
    private function _link_usernames($text)
    {
        $pattern = '';
        $pattern = '/[\s\.\(](@([a-z0-9_]+))\b/i';
        preg_match_all($pattern, ' '.$text.' ', $matches, PREG_SET_ORDER);
        if (PerchUtil::count($matches)) {
            //PerchUtil::debug($matches);
            foreach($matches as $match) {
                $link = ' <a href="http://twitter.com/'.PerchUtil::html($match[2]).'">'.PerchUtil::html($match[1]).'</a>';
                $text = str_replace(trim($match[0]), $link, $text);
            }
        }
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Process tweet to turn #hashtags into links
     *
     * @param string $text 
     * @return void
     * @author Drew McLellan
     */
    private function _link_hashtags($text)
    {
        $pattern = '';
        $pattern = '/([\s\(]+)(#([a-z0-9_]*))\b/i';
        preg_match_all($pattern, ' '.$text, $matches, PREG_SET_ORDER);
        if (PerchUtil::count($matches)) {
            foreach($matches as $match) {
                $link = $match[1].'<a href="http://twitter.com/search?q=%23'.PerchUtil::html($match[3]).'">'.PerchUtil::html($match[2]).'</a>';
                $text = str_replace(trim($match[0]), $link, $text);
            }
        }
        $text = trim($text);
        
        return $text;
    }
    
    
    /*
        Thanks StackOverflow user pix0r
        http://stackoverflow.com/users/72/pix0r
        http://stackoverflow.com/questions/1925455/how-to-mimic-stackoverflow-auto-link-behavior
    */
    private function _auto_link_text($text) {
        $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
        return preg_replace_callback($pattern, array($this, '_auto_link_text_callback'), $text);
    }

    private function _auto_link_text_callback($matches) {
        $max_url_length = 50;
        $max_depth_if_over_length = 2;
        $ellipsis = '&hellip;';

        $url_full = $matches[0];
        $url_short = '';

        if (strlen($url_full) > $max_url_length) {
            $parts = parse_url($url_full);
            $url_short = $parts['scheme'] . '://' . preg_replace('/^www\./', '', $parts['host']) . '/';

            $path_components = explode('/', trim($parts['path'], '/'));
            foreach ($path_components as $dir) {
                $url_string_components[] = $dir . '/';
            }

            if (!empty($parts['query'])) {
                $url_string_components[] = '?' . $parts['query'];
            }

            if (!empty($parts['fragment'])) {
                $url_string_components[] = '#' . $parts['fragment'];
            }

            for ($k = 0; $k < count($url_string_components); $k++) {
                $curr_component = $url_string_components[$k];
                if ($k >= $max_depth_if_over_length || strlen($url_short) + strlen($curr_component) > $max_url_length) {
                    if ($k == 0 && strlen($url_short) < $max_url_length) {
                        // Always show a portion of first directory
                        $url_short .= substr($curr_component, 0, $max_url_length - strlen($url_short));
                    }
                    $url_short .= $ellipsis;
                    break;
                }
                $url_short .= $curr_component;
            }

        } else {
            $url_short = $url_full;
        }

        return "<a rel=\"nofollow\" href=\"".PerchUtil::html($url_full)."\" title=\"".PerchUtil::html($url_full)."\">".PerchUtil::html($url_short)."</a>";
    }
}
