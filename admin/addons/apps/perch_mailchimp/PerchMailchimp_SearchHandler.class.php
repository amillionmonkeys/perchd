<?php

class PerchMailchimp_SearchHandler implements PerchAPI_SearchHandler
{
    
    private static $tmp_url_vars = false;


	public static function get_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_mailchimp');
        $db = $API->get('DB');
        
        $sql = 'SELECT \''.__CLASS__.'\' AS source, MATCH(campaignSubject, campaignHTML) AGAINST('.$db->pdb($key).') AS score, campaignSubject, campaignSlug, campaignSendTime, campaignText, campaignID, "", "", ""
	            FROM '.PERCH_DB_PREFIX.'mailchimp_campaigns 
	            WHERE MATCH(campaignSubject, campaignHTML) AGAINST('.$db->pdb($key).')';
	    
	    return $sql;
    }
    
    public static function get_backup_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_blog');
        $db = $API->get('DB');
        
        $sql = 'SELECT \''.__CLASS__.'\' AS source, campaignSendTime AS score, campaignSubject, campaignSlug, campaignSendTime, campaignText, campaignID, "", "", ""
	            FROM '.PERCH_DB_PREFIX.'mailchimp_campaigns 
	            WHERE  ( 
	                    concat("  ", campaignSubject, "  ") REGEXP '.$db->pdb('[[:<:]]'.$key.'[[:>:]]').' 
                    OR  concat("  ", campaignHTML, "  ") REGEXP '.$db->pdb('[[:<:]]'.$key.'[[:>:]]').'    
                         
	                    ) ';
	    
	    return $sql;
    }
    
    public static function format_result($key, $options, $result)
    {
        $result['campaignSubject']  = $result['col1'];
        $result['campaignSlug']     = $result['col2'];
        $result['campaignSendTime'] = $result['col3'];
        $result['campaignText']     = $result['col4'];
        $result['campaignID']       = $result['col5'];
        $result['_id']              = $result['col5'];
        
        $Settings   = PerchSettings::fetch();
        
        $html = PerchUtil::excerpt_char($result['campaignText'], $options['excerpt-chars'], true);
        // keyword highlight
        $html = preg_replace('/('.$key.')/i', '<span class="keyword">$1</span>', $html);
                        
        $match = array();
        
        $match['url']     = $Settings->get('perch_mailchimp_campaign_url')->settingValue();
        self::$tmp_url_vars = $result;
        $match['url'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array('self', "substitute_url_vars"), $match['url']);
        self::$tmp_url_vars = false;
        
        $match['title']   = $result['campaignSubject'];
        $match['excerpt'] = $html;
        $match['key']     = $key;
        return $match;
    }
    
    private static function substitute_url_vars($matches)
	{
	    $url_vars = self::$tmp_url_vars;
    	if (isset($url_vars[$matches[1]])){
    		return $url_vars[$matches[1]];
    	}
	}
    
}

?>