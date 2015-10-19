<?php

class PerchMembers_Tags extends PerchAPI_Factory
{
    protected $table     = 'members_tags';
	protected $pk        = 'tagID';
	protected $singular_classname = 'PerchMembers_Tag';
	
	protected $default_sort_column = 'tag';
	

	public function find_or_create($tag, $display=false)
	{
		if (!$display) $display=$tag;

		$sql = 'SELECT * FROM '.$this->table.' WHERE tag='.$this->db->pdb($tag).' LIMIT 1';
		$row = $this->db->get_row($sql);

		if (PerchUtil::count($row)) {
			return $this->return_instance($row);
		}

		// Tag wasn't found, so create a new one and return it.

		$data = array();
		$data['tag'] = $tag;
		$data['tagDisplay'] = $display;

		return $this->create($data);
	}

    public function find_by_tag($tag)
    {
        $sql = 'SELECT * FROM '.$this->table.' WHERE tag='.$this->db->pdb($tag).' LIMIT 1';
        $row = $this->db->get_row($sql);

        if (PerchUtil::count($row)) {
            return $this->return_instance($row);
        }

        return false;
    }

	public function get_for_member($memberID)
    {
        $sql = 'SELECT t.*, mt.tagExpires
                FROM '.PERCH_DB_PREFIX.'members_member_tags mt, '.PERCH_DB_PREFIX.'members_tags t
                WHERE mt.tagID=t.tagID AND mt.memberID='.$this->db->pdb($memberID).'
                ORDER BY t.tag ASC';
        return $this->return_instances($this->db->get_rows($sql));
    }

    public function remove_from_member($memberID, $exceptions=array())
    {
    	$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_member_tags 
    			WHERE memberID='.$this->db->pdb($memberID);

    	if (PerchUtil::count($exceptions)) {
    		$sql .= ' AND tagID NOT IN ('.$this->db->implode_for_sql_in($exceptions).') ';
    	}

    	$this->db->execute($sql);
    }

    /**
     * Parse a string of entered tags (e.g. "this, that, the other") into an array of tags
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function parse_string($str)
    {
    	$tags = explode(',', $str);
    	$out = array();
    	if (PerchUtil::count($tags)) {
    		foreach($tags as $tag) {
    			$out[] = array(
    				'tag'=>PerchUtil::urlify(trim($tag)),
    				'tagDisplay'=>trim($tag)
    			);
    		}
    	}

    	return $out;
    }

}
