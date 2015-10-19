<?php

class PerchMembers_Session
{
	static protected $instance;

	public $logged_in = false;


	private $details = array();

	public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}

	public function load($row)
	{
		$this->details = $row;
	}

	public function get($property)
	{
		if ($this->logged_in) {

			switch($property) 
			{
				case 'email': 	$property = 'memberEmail'; 		break;
				case 'status': 	$property = 'memberStatus'; 	break;
				case 'expires': $property = 'memberExpires'; 	break;
				case 'auth_id': $property = 'memberAuthID'; 	break;
				case 'id': 		$property = 'memberID'; 		break;
			}

			if (isset($this->details[$property])) {
				return $this->details[$property];
			}


		}

		return false;
	}

	public function has_tag($tag)
	{
		if ($this->logged_in) {

			if (isset($this->details['tags']) && is_array($this->details['tags'])) {
				return in_array($tag, $this->details['tags']);
			}

		}

		return false;
	}

	public function to_array()
	{
		$out = $this->details;

		$out['email']   = (isset($out['memberEmail']) 	? $out['memberEmail'] 	: '');
		$out['status']  = (isset($out['memberStatus']) 	? $out['memberStatus'] 	: '');
		$out['expires'] = (isset($out['memberExpires']) ? $out['memberExpires'] : '');
		$out['auth_id'] = (isset($out['memberAuthID']) 	? $out['memberAuthID'] 	: '');
		$out['id']      = (isset($out['memberID']) 		? $out['memberID'] 		: '');

		return $out;
	}

	public function get_tags()
	{
		if ($this->logged_in) {

			if (isset($this->details['tags'])) {
				return $this->details['tags'];
			}

		}

		return false;

	}


}
