<?php

class PerchComments_Comments extends PerchAPI_Factory
{
	protected $table     = 'comments';
	protected $pk        = 'commentID';
	protected $singular_classname = 'PerchComments_Comment';

	protected $default_sort_column = 'commentScore';

	public $static_fields   = array('parentID', 'commentName', 'commentEmail', 'commentURL', 'commentIP', 'commentDateTime', 'commentHTML', 'commentStatus', 'commentEnvironment', 'commentScore', 'parentTitle');

	/**
	 * Get count of comments of the given type.
	 *
	 * @param string $status
	 * @return void
	 * @author Drew McLellan
	 */
	public function get_count($status=false)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table;

		if ($status) $sql .=' WHERE commentStatus='.$this->db->pdb($status);

		return $this->db->get_count($sql);
	}

	public function get_count_for_parent($parentID, $status=false)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE parentID='.$this->db->pdb($parentID);

		if ($status) $sql .=' AND commentStatus='.$this->db->pdb($status);

		return $this->db->get_count($sql);
	}


	/**
	 * Get comments by their status (pending, live, rejected, spam or ALL)
	 *
	 * @param string $status
	 * @param string $Paging
	 * @return void
	 * @author Drew McLellan
	 */
	public function get_by_status($status='pending', $Paging=false)
	{
		$status = strtoupper($status);

		if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' c.*
                FROM ' . $this->table .' c
				WHERE 1=1 ';

		if ($status != 'ALL') $sql .= ' AND c.commentStatus='.$this->db->pdb($status);

		$sql .= ' ORDER BY c.commentDateTime DESC ';

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
	}


	public function find_with_status($commentID, $status='pending')
	{
		$status = strtoupper($status);

		$sql = 'SELECT c.*
                FROM ' . $this->table .' c
				WHERE commentID='.$this->db->pdb($commentID);

		if ($status != 'ALL') $sql .= ' AND c.commentStatus='.$this->db->pdb($status);

		$sql .= ' LIMIT 1';

        $result = $this->db->get_row($sql);

        return $this->return_instance($result);
	}


	public function get_custom($parentID, $opts)
	{
		$filter_type = 'php';
		$single_mode = false;
		$select = 'SELECT ';
		$where = array();
		$order = array();
        $limit = '';

		$sql = ' * FROM '.$this->table;

		$where[] = 'parentID='.$this->db->pdb($parentID);
		$where[] = 'commentStatus='.$this->db->pdb('LIVE');

		if (isset($opts['_id'])) {
			$where[] = 'commentID='.$this->db->pdb((int)$opts['_id']);
			$single_mode = true;
		}

		if (!$single_mode && isset($opts['filter']) && in_array($opts['filter'], $this->static_fields)) {
			$filter_type = 'sql';

			$key = $opts['filter'];
            $raw_value = $opts['value'];
            $value = $this->db->pdb($opts['value']);

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
                    $where[] = $key." REGEXP '/\b".$v."\b/i'";
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
                    if (PerchUtil::count($vals)) {
                        $where[] = $key.' IN ('.$this->implode_for_sql_in($vals).') ';
                    }
                    break;
            }


		    // limit
		    if (isset($opts['count'])) {
		        $count = (int) $opts['count'];

		        if (isset($opts['start'])) {
	                $start = (((int) $opts['start'])-1). ',';
		        }else{
		            $start = '';
		        }

		        $limit = $start.$count;
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
            $order[] = 'RAND()';
        }




        // Paging
        $Paging = $this->api->get('Paging', $opts['pagination-var']);
        if ($opts['paginate']==false || (isset($opts['start']) && $opts['start']!='')) {
            $Paging->disable();
        }else{
            $Paging->set_per_page($opts['count']);
            if (isset($opts['start']) && $opts['start']!='') {
                $Paging->set_start_position($opts['start']);
            }
        }

	    $sql = $Paging->select_sql() . $sql;


    	$sql .= ' WHERE 1=1 ';

	    if (count($where)) {
	        $sql .= ' AND ' . implode(' AND ', $where);
	    }

	    if (count($order)) {
	        $sql .= ' ORDER BY '.implode(', ', $order);
	    }

        if ($filter_type=='sql' && $Paging->enabled()) {
            $sql .= ' '.$Paging->limit_sql();
        }else{
            if ($limit && $limit!='') {
    	        $sql .= ' LIMIT '.$limit;
    	    }
        }

	    $rows    = $this->db->get_rows($sql);

	    if ($Paging->enabled()) {
	        $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
	    }

	    $objects  = $this->return_instances($rows);

    	$comments = array();
        if (PerchUtil::count($objects)) {
            foreach($objects as $Object) $comments[] = $Object->to_array();
        }


        // if not filtering by a column in SQL
	    if ($filter_type=='php') {
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            if (PerchUtil::count($comments)) {
    	            $out = array();
    	            $key = $opts['filter'];
    	            $val = $opts['value'];
    	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
    	            foreach($comments as $item) {
    	            	if (!isset($item[$key])) $item[$key] = false;
    	                if (isset($item[$key])) {
    	                    switch ($match) {
                                case 'eq':
                                case 'is':
                                case 'exact':
                                    if ($item[$key]==$val) $out[] = $item;
                                    break;
                                case 'neq':
                                case 'ne':
                                case 'not':
                                    if ($item[$key]!=$val) $out[] = $item;
                                    break;
                                case 'gt':
                                    if ($item[$key]>$val) $out[] = $item;
                                    break;
                                case 'gte':
                                    if ($item[$key]>=$val) $out[] = $item;
                                    break;
                                case 'lt':
                                    if ($item[$key]<$val) $out[] = $item;
                                    break;
                                case 'lte':
                                    if ($item[$key]<=$val) $out[] = $item;
                                    break;
                                case 'contains':
                                    $value = str_replace('/', '\/', $val);
                                    if (preg_match('/\b'.$value.'\b/i', $item[$key])) $out[] = $item;
                                    break;
                                case 'regex':
                                case 'regexp':
                                    if (preg_match($val, $item[$key])) $out[] = $item;
                                    break;
                                case 'between':
                                case 'betwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>trim($vals[0]) && $item[$key]<trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'eqbetween':
                                case 'eqbetwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>=trim($vals[0]) && $item[$key]<=trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'in':
                                case 'within':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)) {
                                        foreach($vals as $value) {
                                            if ($item[$key]==trim($value)) {
                                                $out[] = $item;
                                                break;
                                            }
                                        }
                                    }
                                    break;

    	                    }
    	                }
    	            }
    	            $comments = $out;
    	        }
	        }


	        // sort
		    if (isset($opts['sort'])) {
		        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
		            $desc = true;
		        }else{
		            $desc = false;
		        }
		        $comments = PerchUtil::array_sort($comments, $opts['sort'], $desc);
		    }

		    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
	            shuffle($comments);
	        }

	        // Pagination
	        if (isset($opts['paginate'])) {

	            $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);

	            $opts['count'] = $Paging->per_page();
	            $opts['start'] = $Paging->lower_bound()+1;

	            $Paging->set_total(PerchUtil::count($comments));
	        }else{
	            $Paging = false;
	        }

	        // limit
		    if (isset($opts['count']) || isset($opts['start'])) {

	            // count
		        if (isset($opts['count']) && $opts['count']) {
		            $count = (int) $opts['count'];
		        }else{
		            $count = PerchUtil::count($comments);
		        }

		        // start
		        if (isset($opts['start'])) {
		            if ($opts['start'] === 'RAND') {
		                $start = rand(0, PerchUtil::count($comments)-1);
		            }else{
		                $start = ((int) $opts['start'])-1;
		            }
		        }else{
		            $start = 0;
		        }

		        // loop through
		        $out = array();
		        for($i=$start; $i<($start+$count); $i++) {
		            if (isset($comments[$i])) {
		                $out[] = $comments[$i];
		            }else{
		                break;
		            }
		        }

			}

		  	$comments = $out;
	    }


        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            return $comments;
	    }

	    // template
	    if (isset($opts['template'])) {
	        $template = 'comments/'.$opts['template'];
	    }else{
	        $template = 'comments/comment.html';
	    }

	   	// Paging to template
        if (is_object($Paging)) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
	        foreach($comments as &$item) {
	            foreach($paging_array as $key=>$val) {
	                $item[$key] = $val;
	            }
	        }
        }

	    $Template = $this->api->get("Template");
	    $Template->set($template, 'comments');

        $html = $Template->render_group($comments, true);

	    return $html;

	}


	public function receive_new_comment($SubmittedForm)
	{
		$input = $SubmittedForm->data;

		if ($input['parentID']) {

			$data = array();
			$data['parentID'] = $input['parentID'];
			$data['commentDateTime'] = date('Y-m-d H:i:s');

			foreach($this->static_fields as $field) {
				if (!isset($data[$field])) {
					if (isset($input[$field]) && $input[$field]!='') {
						$data[$field] = trim($input[$field]);
					}
				}
			}

			// dynamic fields
			$dynamic_fields = array();
			foreach($input as $field=>$val) {
				if (!isset($data[$field])) {
					$dynamic_fields[$field] = trim($val);
				}
			}
			$data['commentDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);

			// Anti-spam
			$Settings = $this->api->get('Settings');
			$akismetAPIKey = $Settings->get('perch_comments_akismet_key')->val();

	        $spam = false;
	        $antispam = $SubmittedForm->get_antispam_values();
	        $environment = $_SERVER;

	        $spam_data = array();
            $spam_data['fields'] = $antispam;
            $spam_data['environment'] = $environment;
            $data['commentSpamData'] = PerchUtil::json_safe_encode($spam_data);

            $data['commentIP'] = ip2long($_SERVER['REMOTE_ADDR']);


	        $spam = $this->_check_for_spam($antispam, $environment, $akismetAPIKey);


	        if ($spam) {
	        	$data['commentStatus'] = 'SPAM';
	        }else{

	        	$Users          = new PerchUsers;
        		$CurrentUser    = $Users->get_current_user();

        		if (is_object($CurrentUser) && $CurrentUser->logged_in()) {
        			$data['commentStatus'] = 'LIVE';
        		}else{
        			$data['commentStatus'] = 'PENDING';
        		}


	        }


	        foreach($data as $key=>$val) {

				switch($key) {

					case 'commentHTML':
				        if (!class_exists('\\Netcarver\\Textile\\Parser', false) && class_exists('Textile', true)) {
				            // sneaky autoloading hack
				        }

				        if (PERCH_HTML5) {
				            $Textile = new \Netcarver\Textile\Parser('html5');
				        }else{
				            $Textile = new \Netcarver\Textile\Parser;
				        }


				        if (PERCH_RWD) {
				            $val  =  $Textile->setDimensionlessImages(true)->textileRestricted($val);
				        }else{
				            $val  =  $Textile->textileRestricted($val);
				        }

				        if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
						    $val = str_replace(' />', '>', $val);
						}
						break;

					case 'commentURL':
						if (!parse_url($val, PHP_URL_SCHEME)) {
							$val = 'http://'.$val;
						}
						if (!parse_url($val, PHP_URL_SCHEME)) {
							$val = '';
						}
						$val = strtolower($val);
						break;

					case 'commentEmail':
						$val = strtolower($val);
						break;

					case 'parentTitle':
						$val = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
						break;



				}

				$data[$key] = $val;

			}

			$r = $this->create($data);

			return $r;
		}

		PerchUtil::debug($SubmittedForm);
	}


	public function receive_new_vote($SubmittedForm)
	{
		$input = $SubmittedForm->data;

		if ($input['commentID']) {
			$Comment = $this->find($input['commentID']);

			if (is_object($Comment)) {

				$Settings = $this->api->get('Settings');

				if ($input['vote']=='up') {
					$value = (int)$Settings->get('perch_comments_upvote')->val();
				}else{
					$value = 0 - (int)$Settings->get('perch_comments_downvote')->val();
				}

				if (isset($_COOKIE[$input['cookie']]) && $_COOKIE[$input['cookie']]!='') {
					$voterID = $_COOKIE[$input['cookie']];
				}else{
					$voterID = $this->_get_new_voterID();
					PerchUtil::setcookie($input['cookie'], $voterID, strtotime('+1 YEAR'), $input['cookie_path']);
				}


				$Comment->register_vote($value, $voterID);
			}
		}

	}

	/**
	 * Delete spam messages older than $days days
	 * @param  int $days Age in days
	 * @return int       Number of items deleted
	 */
	public function delete_old_spam($days)
	{
		$time = strtotime('-'.$days.' DAYS');
		$date = date('Y-m-d H:i:s', $time);

		$sql = 'SELECT COUNT(*) AS qty FROM '.$this->table.' WHERE commentStatus='.$this->db->pdb('SPAM').' AND commentDateTime < '.$this->db->pdb($date);
		$count = $this->db->get_count($sql);

		if ($count>0) {
			$sql = 'DELETE FROM '.$this->table.' WHERE commentStatus='.$this->db->pdb('SPAM').' AND commentDateTime < '.$this->db->pdb($date);
			$this->db->execute($sql);
		}

		return $count;
	}

	public function get_first_pending($excluding_commentID)
	{
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE commentStatus='.$this->db->pdb('PENDING').' AND commentID!='.$this->db->pdb($excluding_commentID).'
				ORDER BY commentDateTime DESC';
		return $this->return_instance($this->db->get_row($sql));
	}


	private function _get_new_voterID()
	{
		$sql = 'SELECT COUNT(DISTINCT voterID) FROM '.PERCH_DB_PREFIX.'comments_votes';
		$val = $this->db->get_value($sql);

		return intval($val, 36);
	}


	private function _check_for_spam($fields, $environment, $akismetAPIKey)
    {
    	if (isset($fields['honeypot']) && trim($fields['honeypot'])!='') {
    		PerchUtil::debug('Honeypot field completed: message is spam');
            return true;
    	}

    	if ($akismetAPIKey) {
	    	if (!class_exists('PerchComments_Akismet')) {
	    		include_once('PerchComments_Akismet.class.php');
	    	}
	        if (PerchComments_Akismet::check_message_is_spam($akismetAPIKey, $fields, $environment)) {
	            PerchUtil::debug('Message is spam');
	            return true;
	        }else{
	            PerchUtil::debug('Message is not spam');
	        }
	    }
        return false;
    }


}
