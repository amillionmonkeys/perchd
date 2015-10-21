<?php

class PerchMailchimp_Campaigns extends PerchAPI_Factory
{
    protected $table     = 'mailchimp_campaigns';
	protected $pk        = 'campaignID';
	protected $singular_classname = 'PerchMailchimp_Campaign';
	
	protected $default_sort_column = 'campaignSendTime';
	

	/**
    * get campaign data from the API and store it in our table
    */
    public function populate($api_key, $list_id, $echo_feedback=false) {

        $MailChimp = new MailChimp($api_key);

        if ($echo_feedback) {
            $API  = new PerchAPI(1.0, 'perch_mailchimp');
            $Lang = $API->get('Lang');
        }

        $opts = array(
            'apikey'=>$api_key,
            'filters'=>array(
                'list_id'=>$list_id,
                'status'=>'sent'
            )
        );

        $result = $MailChimp->call('campaigns/list',$opts);

        if($result && isset($result['total']) && $result['total'] > 0) {
            
            foreach($result['data'] as $item) {

                $campaignID = $item['id'];

                //get the content
                $content_opts = array(
                    'apikey'=>$api_key,
                    'cid'=>$campaignID
                );
                $content = $MailChimp->call('campaigns/content',$content_opts);
                if(isset($content['html'])) {
                    $campaignHTML = $content['html'];
                }
                if(isset($content['text'])) {
                    $campaignText = $content['text'];
                }

                // array for insertion
                $campaign = array(
                    'campaignCID' => $campaignID,
                    'campaignWebID' => $item['web_id'],
                    'campaignTitle' => $item['title'],
                    'campaignCreateTime' => $item['create_time'],
                    'campaignSendTime' => $item['send_time'],
                    'campaignSent' => $item['emails_sent'],
                    'campaignSubject' => $item['subject'],
                    'campaignArchiveURL' => $item['archive_url'],
                    'campaignHTML' => $campaignHTML,
                    'campaignText' => $campaignText,
                    'campaignSlug' => PerchUtil::urlify(date('d M Y', strtotime($item['create_time'])).' '.$item['subject']),
                );

                //insert into our table
                $this->db->insert($this->table, $campaign);

                if ($echo_feedback) {
                    echo '<li class="icon success">';
                    echo $Lang->get('Importing campaign %s (%s)', $item['title'], $item['create_time']);
                    echo '</li>';
                    flush();
                }
            }

        }

    }

    /**
    * return all of the campaign information we have stored.
    */
    public function all_campaigns($Paging=false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }
        
        $sql .= ' * 
                FROM ' . $this->table;
                
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

    public function get_custom($opts)
    {
        $campaigns = array();
        $Campaign = false;
        $single_mode = false;
        $where = array();
        $order = array();
        $limit = '';
        
        
        // find specific _id
        if (isset($opts['_id'])) {
            $single_mode = true;
            $Campaign = $this->find($opts['_id']);
        }else{        
            // if not picking an _id, check for a filter
            if (isset($opts['filter']) && isset($opts['value'])) {
                
                $key       = $opts['filter'];
                $raw_value = $opts['value'];
                $value     = $this->db->pdb($opts['value']);
                
                $match = isset($opts['match']) ? $opts['match'] : 'eq';
                switch ($match) {
                    case 'eq': 
                    case 'is': 
                    case 'exact': 
                        $where[] = $key.'='.$value;
                        break;
                    case 'neq': 
                    case 'ne': 
                    case 'not': 
                        $where[] = $key.'!='.$value;
                        break;
                    case 'gt':
                        $where[] = $key.'>'.$value;
                        break;
                    case 'gte':
                        $where[] = $key.'>='.$value;
                        break;
                    case 'lt':
                        $where[] = $key.'<'.$value;
                        break;
                    case 'lte':
                        $where[] = $key.'<='.$value;
                        break;
                    case 'contains':
                        $v = str_replace('/', '\/', $raw_value);
                        $where[] = $key." REGEXP '[[:<:]]'.$v.'[[:>:]]'";
                        break;
                    case 'regex':
                    case 'regexp':
                        $v = str_replace('/', '\/', $raw_value);
                        $where[] = $key." REGEXP '".$v."'";
                        break;
                    case 'between':
                    case 'betwixt':
                        $vals  = explode(',', $raw_value);
                        if (PerchUtil::count($vals)==2) {
                            $where[] = $key.'>'.trim($this->db->pdb($vals[0]));
                            $where[] = $key.'<'.trim($this->db->pdb($vals[1]));
                        }
                        break;
                    case 'eqbetween':
                    case 'eqbetwixt':
                        $vals  = explode(',', $raw_value);
                        if (PerchUtil::count($vals)==2) {
                            $where[] = $key.'>='.trim($this->db->pdb($vals[0]));
                            $where[] = $key.'<='.trim($this->db->pdb($vals[1]));
                        }
                        break;
                    case 'in':
                    case 'within':
                        $vals  = explode(',', $raw_value);
                        $tmp = array();
                        if (PerchUtil::count($vals)) {
                            foreach($vals as $value) {
                                if ($item[$key]==trim($value)) {
                                    $tmp[] = $item;
                                    break;
                                }
                            }
                            $where[] = $key.' IN '.$this->implode_for_sql_in($tmp);
                        }
                        break;
                }
            }
        }

    
        // sort
        if (isset($opts['sort'])) {
            $desc = false;
            if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
                $desc = true;
            }else{
                $desc = false;
            }
            $order[] = $opts['sort'].' '.($desc ? 'DESC' : 'ASC');
        }
    
        if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            $order = array('RAND()');
        }
    
        // limit
        if (isset($opts['count']) && $opts['count']) {
            $count = (int) $opts['count'];
        
            if (isset($opts['start'])) {
                $start = (((int) $opts['start'])-1). ',';
            }else{
                $start = '';
            }
        
            $limit = $start.$count;
        }
        
        if ($single_mode){
            $campaigns = array($Campaign);
        }else{

            // Paging
            $Paging = $this->api->get('Paging');

            if (isset($opts['pagination-var']) && $opts['pagination-var']!='') {
                $Paging->set_qs_param($opts['pagination-var']);
            }

            if ((!isset($count) || !$count) || (isset($opts['start']) && $opts['start']!='')) {
                $Paging->disable();
            }else{
                $Paging->set_per_page($count);
                if (isset($opts['start']) && $opts['start']!='') {
                    $Paging->set_start_position($opts['start']);
                }
            }
            
            $sql = $Paging->select_sql() . ' p.* FROM '.$this->table.' p WHERE 1=1 ';


            
            if (count($where)) {
                $sql .= ' AND ' . implode(' AND ', $where);
            }
        
            if (count($order)) {
                $sql .= ' ORDER BY '.implode(', ', $order);
            }
            
            if ($Paging->enabled()) {
                $sql .= ' '.$Paging->limit_sql();
            }else{
                if ($limit!='') {
                    $sql .= ' LIMIT '.$limit;
                }
            }
                            
            $rows    = $this->db->get_rows($sql);
            
            if ($Paging->enabled()) {
                $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
            }
                        
            $campaigns  = $this->return_instances($rows);

        }
      
        
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            
            if ($single_mode) return $Campaign;
            
            $out = array();
            if (PerchUtil::count($campaigns)) {
                foreach($campaigns as $Campaign) {
                    $out[] = $Campaign->to_array();
                }
            }
            return $out; 
        }
        
        // template
        $template = 'mailchimp/'.str_replace('mailchimp/', '', $opts['template']);

        
        if (isset($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array();
            // merge in paging vars
            if (PerchUtil::count($campaigns)) {
                foreach($campaigns as &$Campaign) {
                    foreach($paging_array as $key=>$val) {
                        $Campaign->squirrel($key, $val);
                    }
                }
            }
        }
                
        $Template = $this->api->get("Template");
        $Template->set($template, 'mailchimp');

        if (PerchUtil::count($campaigns)) {
            $html = $Template->render_group($campaigns, true);
        }else{
            $Template->use_noresults();
            $html = $Template->render(array());
        }
               

        return $html;
    }
  
}
?>