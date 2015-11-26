<?php
class Members extends PerchAPI_Factory
{
    protected $table     = 'members';
	protected $pk        = 'memberID';
	protected $singular_classname = 'PerchMembers_Member';
	
	protected $default_sort_column = 'memberEmail';

	public function get_members($status, $Paging=false)
	{
		$out = $this->get_by('memberStatus', $status, $Paging);

		$sql = 'SELECT c.*
                FROM ' . $this->table .' c';

		if ($status != 'ALL') $sql .= ' WHERE c.memberStatus='.$this->db->pdb($status);

		$members = $this->db->get_rows($sql);

		foreach ($members as $key=>$value) {
            $dynamic_fields = PerchUtil::json_safe_decode($value['memberProperties'], true);
            foreach($dynamic_fields as $dynamic_fields_key=>$dynamic_fields_value) {
            	$members[$key][$dynamic_fields_key] = $dynamic_fields_value;
            }

		}

        return $members;
	}
}