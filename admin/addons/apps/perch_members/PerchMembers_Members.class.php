<?php

class PerchMembers_Members extends PerchAPI_Factory
{
    protected $table     = 'members';
	protected $pk        = 'memberID';
	protected $singular_classname = 'PerchMembers_Member';
	
	protected $default_sort_column = 'memberEmail';
	
	public $static_fields = array('memberID', 'memberAuthType', 'memberAuthID', 'memberEmail', 'memberPassword', 'memberStatus', 'memberCreated', 'memberExpires', 'memberProperties', );
    public $field_aliases = array(
			'email'   => 'memberEmail',
			'status'  => 'memberStatus',
			'expires' => 'memberExpires',
			'auth_id' => 'memberAuthID',
			'id'      => 'memberID',	
			'password'=> 'memberPassword',
    	);

    public $dynamic_fields_column = 'memberProperties';

    public     $default_fields = '
    				<perch:members type="email" id="memberEmail" required="true" label="Email" listing="true" order="98" />
                    <perch:members type="date" id="memberCreated" required="true" label="Joined" listing="true" format="d F Y" order="99" />
                    <perch:members type="select" id="memberStatus" options="Pending|pending,Active|active,Inactive|inactive" label="Status" listing="true" order="100" />
                    ';


	public function get_by_status($status, $Paging=false)
	{
		return $this->get_by('memberStatus', $status, $Paging);
	}

	public function register_with_form($SubmittedForm)
	{
		$key = $SubmittedForm->id.(isset($SubmittedForm->form_attributes['type'])?'.'.$SubmittedForm->form_attributes['type']:'');

		$Forms = new PerchMembers_Forms($this->api);
		$Form = $Forms->find_or_create($key);

		$do_login = false;

		if (is_object($Form)) {

			$form_settings = PerchUtil::json_safe_decode($Form->formSettings(), true);

			$member = array(
				'memberAuthType' => 'native',
				'memberEmail'    => '',
				'memberPassword' => '',
				'memberStatus'   => 'pending',
				'memberCreated'  => date('Y-m-d H:i:s'),
			);

			$data = $SubmittedForm->data;
			$properties = array();

			foreach($data as $key=>$val) {

	    		if (array_key_exists($key, $this->field_aliases)) {
	    			$member[$this->field_aliases[$key]] = $val;
	    			$key = $this->field_aliases[$key];
	    		}

	    		if (!in_array($key, $this->static_fields)) {
	    			$properties[$key] = $val;
	    		}

	    	}

	    	$member['memberProperties'] = PerchUtil::json_safe_encode($properties);

	    	// Password
	    	if (defined('PERCH_NONPORTABLE_HASHES') && PERCH_NONPORTABLE_HASHES) {
                $portable_hashes = false;
            }else{
                $portable_hashes = true;
            }
            $Hasher = new PasswordHash(8, $portable_hashes);
            $clear_pwd = $member['memberPassword'];

            // auto-generate a password?
            if ($clear_pwd === '__auto__') {
            	$clear_pwd = PerchUser::generate_password(16);
            }

	    	$member['memberPassword'] = $Hasher->HashPassword($clear_pwd);

	    	$Member = $this->create($member);

	    	$member = array(
	    		'memberAuthID'=>$Member->memberID()
	    	);

	    	if (isset($form_settings['moderate']) && $form_settings['moderate']=='1') {
	    		if (isset($form_settings['moderator_email'])) {
	    			$this->_email_moderator($form_settings['moderator_email'], $Member);
	    		}
	    	}else{
	    		$member['memberStatus'] = 'active';
	    		$do_login = true;
	    	}

	    	$Member->update($member);

	    	if (isset($form_settings['default_tags']) && $form_settings['default_tags']!='') {
	    		$tags = explode(',', $form_settings['default_tags']);
	    		if (PerchUtil::count($tags)) {
	    			foreach($tags as $tagDisplay) {

	    				$expiry = false;

	    				if (strpos($tagDisplay, '|')>0) {
	    					$parts = explode('|', $tagDisplay);
	    					$tagDisplay = $parts[0];
	    					$expiry 	= $parts[1];
	    				}

	    				$tagDisplay = trim($tagDisplay);
	    				$tag = PerchUtil::urlify($tagDisplay);

	    				$Member->add_tag($tag, $tagDisplay, $expiry);
	    			}
	    		}
	    	}

	    	if (is_object($Member) && $do_login) {
	    		$key = base64_encode('login:perch_members:login/login_form.html');
	    		$data = array(
	    					'email'    => $Member->memberEmail(),
	    					'password' => $clear_pwd,
	    				);
	    		$files = array();
	    		$Perch = Perch::fetch();
	    		$Perch->dispatch_form($key, $data, $files);
	    	}

	    	PerchUtil::debug($Member, 'error');

		}
	}


	public static function check_email($email)
	{
		$API  = new PerchAPI(1.0, 'perch_members'); 
		$db	= $API->get('DB');


		$sql = 'SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'members WHERE memberEmail='.$db->pdb($email);
    	$count = $db->get_count($sql);

    	if ($count===0) {
    		return true;
    	}

    	return false;
	}

	public static function check_email_exists($email)
	{
		$API  = new PerchAPI(1.0, 'perch_members'); 
		$db	= $API->get('DB');


		$sql = 'SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'members WHERE memberEmail='.$db->pdb($email);
    	$count = $db->get_count($sql);

    	if ($count) {
    		return true;
    	}

    	return false;
	}


	public function get_by_tag_for_admin_listing($tag)
	{
		$sql = 'SELECT m.* FROM '.$this->table.' m, '.PERCH_DB_PREFIX.'members_member_tags mt, '.PERCH_DB_PREFIX.'members_tags t
				WHERE m.memberID=mt.memberID AND mt.tagID=t.tagID
					AND (mt.tagExpires>='.$this->db->pdb(date('Y-m-d H:i:s')).' OR mt.tagExpires IS NULL)
					AND t.tag='.$this->db->pdb($tag);
		return $this->return_instances($this->db->get_rows($sql));
	}

	public function get_count($status=false)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table;

		if ($status) $sql .=' WHERE memberStatus='.$this->db->pdb($status);
		
		return $this->db->get_count($sql);
	}


	public function reset_member_password($SubmittedForm)
	{
		if (isset($SubmittedForm->data['email'])) {
			$email = $SubmittedForm->data['email'];

			$Member = $this->get_one_by('memberEmail', $email);

			if (is_object($Member)) {
				return $Member->reset_password();
			}

		}
		
		return false;
	}

	public function get_edit_columns()
	{

		$Template   = $this->api->get('Template');
		$Template->set('members/member.html', 'members', $this->default_fields);

	    $tags = $Template->find_all_tags_and_repeaters('members');

	    $out = array();

	    if (PerchUtil::count($tags)) {
	    	foreach($tags as $Tag) {
	    		if ($Tag->listing()) {
	    			$out[] = array(
	    			            'id'=>$Tag->id(),
	    			            'title'=>$Tag->label(),
	    			            'Tag'=>$Tag,
	    			        );
	    		}
	    	}
	    }
	    return $out;

	}

	protected function _email_moderator($email, $Member)
	{
	
		$edit_url = PERCH_LOGINPATH.'/addons/apps/perch_members/edit/?id='.$Member->id();

        $Email = $this->api->get('Email');
        $Email->set_template('members/emails/new_member_notification.html');
        $Email->set_bulk($Member->to_array());
        $Email->set('url', $edit_url);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($email);
        $Email->send();
	}

}
