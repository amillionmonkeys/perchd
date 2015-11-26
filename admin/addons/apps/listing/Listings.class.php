<?php

class Listings extends PerchAPI_Factory
{
	protected $table     = 'listings';
	protected $pk        = 'listingID';
	protected $singular_classname = 'listing';

	protected $default_sort_column = 'listingDateTime';

	public $static_fields   = array('listingID', 'listingSlug', 'listingTitle', 'listingType', 'listingDateTime', 'listingHTML', 'listingStatus', 'listingDynamicFields');

	/**
	 * Get count of listing of the given type.
	 *
	 * @param string $status
	 * @return void
	 * @author Phil Smith
	 */
	public function get_count($status=false)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table;

		if ($status) $sql .=' WHERE listingStatus='.$this->db->pdb($status);

		return $this->db->get_count($sql);
	}



	/**
	 * Get listing by their status (pending, live, rejected, spam or ALL)
	 *
	 * @param string $status
	 * @param string $Paging
	 * @return void
	 * @author Phil Smith
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

		if ($status != 'ALL') $sql .= ' AND c.listingStatus='.$this->db->pdb($status);

		$sql .= ' ORDER BY c.listingDateTime DESC ';

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
	}


	public function find_with_status($listingSlug, $status='pending')
	{
		$status = strtoupper($status);

		$sql = 'SELECT c.*
                FROM ' . $this->table .' c
				WHERE listingSlug='.$this->db->pdb($listingSlug);

		if ($status != 'ALL') $sql .= ' AND c.listingStatus='.$this->db->pdb($status);

		$sql .= ' LIMIT 1';

        $result = $this->db->get_row($sql);

        return $this->return_instance($result);
	}


	public function get_custom($listingType, $opts, $listingStatus=false)
	{
		$filter_type = 'php';
		$single_mode = false;
		$select = 'SELECT ';
		$where = array();
		$order = array();
        $limit = '';

		$sql = ' * FROM '.$this->table;

		$where[] = 'listingType='.$this->db->pdb($listingType);
		if($listingStatus){
			$where[] = 'listingStatus='.$this->db->pdb($listingStatus);			
		}


		if (isset($opts['_id'])) {
			$where[] = 'listingID='.$this->db->pdb((int)$opts['_id']);
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

    	$listing = array();
        if (PerchUtil::count($objects)) {
            foreach($objects as $Object) $listing[] = $Object->to_array();
        }


        // if not filtering by a column in SQL
	    if ($filter_type=='php') {
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            if (PerchUtil::count($listing)) {
    	            $out = array();
    	            $key = $opts['filter'];
    	            $val = $opts['value'];
    	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
    	            foreach($listing as $item) {
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
    	            $listing = $out;
    	        }
	        }


	        // sort
		    if (isset($opts['sort'])) {
		        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
		            $desc = true;
		        }else{
		            $desc = false;
		        }
		        $listing = PerchUtil::array_sort($listing, $opts['sort'], $desc);
		    }

		    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
	            shuffle($listing);
	        }

	        // Pagination
	        if (isset($opts['paginate'])) {

	            $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);

	            $opts['count'] = $Paging->per_page();
	            $opts['start'] = $Paging->lower_bound()+1;

	            $Paging->set_total(PerchUtil::count($listing));
	        }else{
	            $Paging = false;
	        }

	        // limit
		    if (isset($opts['count']) || isset($opts['start'])) {

	            // count
		        if (isset($opts['count']) && $opts['count']) {
		            $count = (int) $opts['count'];
		        }else{
		            $count = PerchUtil::count($listing);
		        }

		        // start
		        if (isset($opts['start'])) {
		            if ($opts['start'] === 'RAND') {
		                $start = rand(0, PerchUtil::count($listing)-1);
		            }else{
		                $start = ((int) $opts['start'])-1;
		            }
		        }else{
		            $start = 0;
		        }

		        // loop through
		        $out = array();
		        for($i=$start; $i<($start+$count); $i++) {
		            if (isset($listing[$i])) {
		                $out[] = $listing[$i];
		            }else{
		                break;
		            }
		        }

			}

		  	$listing = $out;
	    }


        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            return $listing;
	    }

	    // template
	    if (isset($opts['template'])) {
	        $template = 'listings/'.$opts['template'];
	    }else{
	        $template = 'listings/listing.html';
	    }

	   	// Paging to template
        if (is_object($Paging)) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
	        foreach($listing as &$item) {
	            foreach($paging_array as $key=>$val) {
	                $item[$key] = $val;
	            }
	        }
        }

	    $Template = $this->api->get("Template");
	    $Template->set($template, 'listing');

        $html = $Template->render_group($listing, true);

	    return $html;

	}


	public function receive_new_listing($SubmittedForm)
	{
		$API  = new PerchAPI(1.0, 'perch_members');
        $Session = PerchMembers_Session::fetch();

		$input = $SubmittedForm->data;
		$data = array();
		$data['listingDateTime'] = date('Y-m-d H:i:s');
		$data['memberID'] = $Session->get('memberID');
		$data['listingType'] = $input['listingType'];
		$data['listingTitle'] = $input['listingTitle'];
		$data['listingSlug'] = PerchUtil::urlify($input['listingTitle']);

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
		$data['listingDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);

        foreach($data as $key=>$val) {

			switch($key) {

				case 'listingHTML':
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



			}

			$data[$key] = $val;

		}
		// print_r($data);
		// die();

		if(isset($data['listingID'])){
			if ($this->check_title_exists($data['listingTitle'], $data['listingID'])){
				$Listings = new Listings($API);
	            $Listing = $Listings->find($data['listingID']);

	            // Don't allow people to change their URL
				unset($data['listingSlug']);
				$r = $Listing->update($data);
			} else {
				$SubmittedForm->throw_error('duplicate', 'listingTitle');
				return false;
			}
		} else {
			if ($this->check_title_exists($data['listingTitle'])){
				$r = $this->create($data);
			} else {
				$SubmittedForm->throw_error('duplicate', 'listingTitle');
				return false;
			}
		}

		return $r;
		
		PerchUtil::debug('this'.$SubmittedForm);
	}

	public static function check_title_exists($listingTitle, $listingID = false)
	{

		$API  = new PerchAPI(1.0, 'listing'); 
		$db	= $API->get('DB');

		$sql = 'SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'listings WHERE listingSlug='.$db->pdb(PerchUtil::urlify($listingTitle)).' AND listingID!='.$db->pdb($listingID);

    	$count = $db->get_count($sql);

    	PerchUtil::debug($sql);

    	if ($count) {
    		return false;
    	}

    	return true;
	}

	

	public function get_first_pending($excluding_listingID)
	{
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE listingStatus='.$this->db->pdb('PENDING').' AND listingID!='.$this->db->pdb($excluding_listingID).'
				ORDER BY listingDateTime DESC';
		return $this->return_instance($this->db->get_row($sql));
	}

}
