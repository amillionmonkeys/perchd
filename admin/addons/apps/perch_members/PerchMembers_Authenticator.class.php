<?php

class PerchMembers_Authenticator extends PerchAPI_Factory
{
	protected $table     = 'members';
	protected $pk        = 'memberID';
	protected $singular_classname = 'PerchMembers_Member';

	protected $messages = array();


	public function form_login($SubmittedForm)
	{
		PerchUtil::debug('Method form_login not implemented in '.get_class($this));
	}


	protected function verify_user($authType='native', $authID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE memberAuthType='.$this->db->pdb($authType).' AND memberAuthID='.$this->db->pdb($authID).' LIMIT 1';
		$result = $this->db->get_row($sql);

		if (PerchUtil::count($result)) {

			// check active
			if ($result['memberStatus']!='active') {
				$this->messages[] = ('This account has been suspended.');
				return false;
			}

			// check expiry date
			if ($result['memberExpires']!='' && strtotime($result['memberExpires'])<time()) {
				$this->messages[] = ('This account has expired.');
				return false;
			}

			return $result;


		}else{
			$this->messages[] = ('Unknown user.');
		}

		return false;
	}



	public function get_messages()
	{
		return $this->messages;
	}

}
