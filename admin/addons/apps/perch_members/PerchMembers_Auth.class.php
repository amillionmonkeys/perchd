<?php

class PerchMembers_Auth extends PerchAPI_Factory
{
	protected $table     = 'members';
	protected $pk        = 'memberID';
	protected $singular_classname = 'PerchMembers_Member';


	private $authenticator_path=''; 

	function __construct($api=false)
    {
    	$this->authenticator_path = PerchUtil::file_path(PERCH_PATH.'/addons/apps/perch_members/authenticators/');
    	parent::__construct($api);
    }


	/**
	 * Process the login form, calling the appropriate authenticator as required.
	 * @param  [type] $SubmittedForm [description]
	 * @return [type]                [description]
	 */
	public function handle_login($SubmittedForm)
	{
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) return true;
				

		$authenticator = 'native';
		if (isset($SubmittedForm->data['authenticator'])) {
			$authenticator = $SubmittedForm->data['authenticator'];
		}

		$class = 'PerchMembers_Authenticator_'.$authenticator;

		$user_path = realpath(PerchUtil::file_path($this->authenticator_path.$authenticator));

		if (PerchUtil::file_path(substr($user_path, 0, strlen($this->authenticator_path))) == PerchUtil::file_path($this->authenticator_path)) {

			$path = PerchUtil::file_path($this->authenticator_path.$authenticator.'/'.$class.'.class.php');

			if (file_exists($path)) {
				include($path);
				$Authenticator = new $class($this->api);

				if (is_object($Authenticator)) {
					$user_row = $Authenticator->form_login($SubmittedForm);

					if ($user_row) {
						PerchUtil::debug('log them in');
						if (isset($user_row['memberPassword'])) unset($user_row['memberPassword']);
						$this->_generate_session($user_row);
						$this->recover_session();

						if (isset($SubmittedForm->data['r']) && $SubmittedForm->data['r']!='') {
							PerchUtil::redirect($SubmittedForm->data['r']);
						}

					}else{
						PerchUtil::debug($Authenticator->get_messages());
					}


				}
			}else{
				PerchUtil::debug('Authenticator '.$class.' not found.', 'error');
			}
			


		}else{
			PerchUtil::debug('Invalid authenticator path: '.PerchUtil::file_path($this->authenticator_path.$authenticator), 'error');
		}

	}

	public function recover_session()
	{
		if (isset($_COOKIE[PERCH_MEMBERS_COOKIE]) && $_COOKIE[PERCH_MEMBERS_COOKIE]!='') {
			$session_id 	= $_COOKIE[PERCH_MEMBERS_COOKIE];
			$http_footprint = $this->_get_http_footprint();

			$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'members_sessions WHERE sessionID='.$this->db->pdb($session_id).' AND sessionHttpFootprint='.$this->db->pdb($http_footprint).' AND sessionExpires>'.$this->db->pdb(date('Y-m-d H:i:s')).' LIMIT 1';
			$row = $this->db->get_row($sql);

			if (PerchUtil::count($row)) {
				$this->_populate_session($row);
			}else{
				$this->_destroy_session($session_id);	
			}
		}
	}


	/**
	 * Create a new session, add it to the session store, set the cookie, expire any old sessions
	 * @param  integer $memberID [description]
	 * @return [type]            [description]
	 */
	protected function _generate_session($user_row=false)
	{
		$session_id  	= sha1(uniqid(mt_rand(), true));
		$http_footprint = $this->_get_http_footprint();

		if (PerchUtil::count($user_row)) {
			$memberID = $user_row['memberID'];

			if (isset($user_row['memberProperties']) && $user_row['memberProperties']!='') {
				$properties = PerchUtil::json_safe_decode($user_row['memberProperties'], true);

				$user_row = array_merge($properties, $user_row);

				unset($user_row['memberProperties']);
			}

			$session_data = $user_row;

			$session_data['tags'] = $this->_load_tags($memberID);
			$session_data['token'] = $this->_generate_csrf_token($session_id);


		}else{
			$memberID = 0;
			$session_data = array();
		}

		$data = array(
			'sessionID'            => $session_id,
			'sessionExpires'       => date('Y-m-d H:i:s', strtotime(' + '.PERCH_MEMBERS_SESSION_TIME)),
			'sessionHttpFootprint' => $http_footprint,
			'memberID'             => $memberID,
			'sessionData'          => serialize($session_data)
			);

		if ($this->db->insert(PERCH_DB_PREFIX.'members_sessions', $data)) {
			PerchUtil::setcookie(PERCH_MEMBERS_COOKIE, $session_id, '', '/', '', '', true);
			$_COOKIE[PERCH_MEMBERS_COOKIE] = $session_id;

			$this->_expire_old_sessions();
		}

		
	}

	public function refresh_session_data($Member=false)
	{
		if ($Member===false) {
			$Members = new PerchMembers_Members($this->api);
			$Session = PerchMembers_Session::fetch();
			$Member = $Members->find($Session->get('memberID'));
		}
 


		if (isset($_COOKIE[PERCH_MEMBERS_COOKIE]) && $_COOKIE[PERCH_MEMBERS_COOKIE]!='') {
			$old_session = $_COOKIE[PERCH_MEMBERS_COOKIE];
		}

		$this->_generate_session($Member->get_details());

		if ($old_session) {
			$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_sessions WHERE sessionID='.$this->db->pdb($old_session);
			$this->db->execute($sql);
		}

		$this->recover_session();
		
	}

	/**
	 * Generate the HTTP Footprint from the HTTP headers
	 * @return [type] [description]
	 */
	protected function _get_http_footprint()
	{
		$http_ua 	 = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown');
		// Turns out Accept changes for ajax requests, so dropping it for now.
		$http_accept = ''; //(isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'unknown');

		return sha1($http_ua.$http_accept);
	}


	/**
	 * Delete any session rows that have expired
	 * @return [type] [description]
	 */
	protected function _expire_old_sessions()
	{
		$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_sessions WHERE sessionExpires < '.$this->db->pdb(date('Y-m-d H:i:s'));
		$this->db->execute($sql);
	}

	/**
	 * Populate the session with all the information about this user
	 * @param  boolean $row [description]
	 * @return [type]       [description]
	 */
	protected function _populate_session($row=false)
	{
		$Session = PerchMembers_Session::fetch();

		$data = unserialize($row['sessionData']);
		$data['sessionID'] = $row['sessionID'];

		$Session->load($data);
		$Session->logged_in = true;

		PerchUtil::debug('User is logged in', 'auth');
		//PerchUtil::debug($Session);
	}

	/**
	 * Kill the session
	 * @return [type] [description]
	 */
	protected function _destroy_session($session_id=false)
	{
		PerchUtil::debug('destroying session');

		if ($session_id) {
			$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_sessions WHERE sessionID='.$this->db->pdb($session_id);
			$this->db->execute($sql);
		}
		
		PerchUtil::setcookie(PERCH_MEMBERS_COOKIE, '', 0, '/', '', '', true);
	}

	public function log_out()
	{
		$Session = PerchMembers_Session::fetch();

		$session_id = $Session->get('sessionID');

		$Session->logged_in = false;
		$Session->load(array());

		$this->_destroy_session($session_id);

		return;
	}

	protected function _load_tags($memberID)
	{
		$sql = 'SELECT t.tag FROM '.PERCH_DB_PREFIX.'members_tags t, '.PERCH_DB_PREFIX.'members_member_tags mt
				WHERE t.tagID=mt.tagID AND mt.memberID='.$this->db->pdb($memberID).' AND (mt.tagExpires IS NULL OR mt.tagExpires>'.$this->db->pdb(date('Y-m-d H:i:00')).')';
		return $this->db->get_rows_flat($sql);
	}

	public function encrypt_new_password($memberID, $old_clear_pwd, $new_clear_pwd) 
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE memberID='.$this->db->pdb($memberID);
		$row = $this->db->get_row($sql);

		if (PerchUtil::count($row)) {

			$authenticator = $row['memberAuthType'];
			$class = 'PerchMembers_Authenticator_'.$authenticator;

			$user_path = realpath(PerchUtil::file_path($this->authenticator_path.$authenticator));

			if (substr($user_path, 0, strlen($this->authenticator_path)) == $this->authenticator_path) {
				$path = PerchUtil::file_path($this->authenticator_path.$authenticator.'/'.$class.'.class.php');
				if (file_exists($path)) {
					include($path);
					$Authenticator = new $class($this->api);
					if (is_object($Authenticator)) {
						return $Authenticator->encrypt_new_password($row, $old_clear_pwd, $new_clear_pwd);
					}else{
						PerchUtil::debug('Failed to create authenticator object', 'error');
					}
				}else{
					PerchUtil::debug('Authenticator file does not exist: '.$path, 'error');
				}
			}else{
				PerchUtil::debug('Bad authenticator path', 'error');
			}


		}

		return false;
	}

	protected function _generate_csrf_token($seed)
	{
		return sha1($seed.uniqid(mt_rand(), true));
	}
}

