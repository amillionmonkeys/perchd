<?php

class PerchMembers_Member extends PerchAPI_Base
{
    protected $table  = 'members';
    protected $pk     = 'memberID';

    public $static_fields = array('memberID', 'memberAuthType', 'memberAuthID', 'memberEmail', 'memberPassword', 'memberStatus', 'memberCreated', 'memberExpires', 'memberProperties', );
    public $field_aliases = array(
			'email'   => 'memberEmail',
			'status'  => 'memberStatus',
			'expires' => 'memberExpires',
			'auth_id' => 'memberAuthID',
			'id'      => 'memberID',	
    	);


    public function delete()
    {
        $this->db->execute('DELETE FROM '.PERCH_DB_PREFIX.'members_member_tags WHERE memberID='.$this->id());

        return parent::delete();
    }

    public function update_profile($SubmittedForm) 
    {
    	$data = $SubmittedForm->data;

    	$out = array();
    	$properties = PerchUtil::json_safe_decode($this->memberProperties(), true);

    	foreach($data as $key=>$val) {

    		if (array_key_exists($key, $this->field_aliases)) {
    			$out[$this->field_aliases[$key]] = $val;
    			$key = $this->field_aliases[$key];
    		}

    		if (!in_array($key, $this->static_fields)) {
    			$properties[$key] = $val;
    		}

    	}

    	if (isset($out['memberEmail'])) {
    		if (!$this->check_email_unique($out['memberEmail'])) {
    			unset($out['memberEmail']);
    		}
		}

    	$out['memberProperties'] = PerchUtil::json_safe_encode($properties);

    	$this->update($out);

    }

    public function add_tag($tag, $tagDisplay=false, $tagExpiry=false)
    {
        $Tags = new PerchMembers_Tags;

        $Tag = $Tags->find_or_create($tag, $tagDisplay);

        $data = array();
        $data['memberID'] = $this->id();
        $data['tagID'] = $Tag->id();
        
        if ($tagExpiry) {
            $data['tagExpires'] = date('Y-m-d H:i:s', strtotime($tagExpiry));
        }

        $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_member_tags WHERE memberID='.$this->db->pdb($this->id()).' AND tagID='.$this->db->pdb($Tag->id());
        $this->db->execute($sql);

        $this->db->insert(PERCH_DB_PREFIX.'members_member_tags', $data);
    }


    public function reset_password($send_notification_email=true)
    {
        $clear_pwd = $this->_generate_password();
        $data = array();

        if (defined('PERCH_NONPORTABLE_HASHES') && PERCH_NONPORTABLE_HASHES) {
            $portable_hashes = false;
        }else{
            $portable_hashes = true;
        }
        $Hasher = new PasswordHash(8, $portable_hashes);
        $data['memberPassword'] = $Hasher->HashPassword($clear_pwd);

        $this->update($data);

        if ($send_notification_email) $this->_email_new_password($clear_pwd);

        return true;

    }

    public function change_password($SubmittedForm)
    {
        $data = $SubmittedForm->data;
        if (isset($data['old_password']) && isset($data['password'])) {
            $old_clear_pwd = $data['old_password'];    
            $new_clear_pwd = $data['password'];

            // check existing password
            $API  = new PerchAPI(1.0, 'perch_members');
            $Session = PerchMembers_Session::fetch();
            $PerchMembers_Auth = new PerchMembers_Auth($API);

            $new_password = $PerchMembers_Auth->encrypt_new_password($Session->get('memberID'), $old_clear_pwd, $new_clear_pwd);

            if ($new_password) {
                $this->update(array('memberPassword'=>$new_password));
                return true;
            }else{
                $SubmittedForm->throw_error('valid', 'old_password');
                return false;
            }
        }
        return false;
    }

    public function to_array()
    {
        $details = $this->details;

        if (isset($details['memberProperties']) && $details['memberProperties']!='') {
            $properties = PerchUtil::json_safe_decode($details['memberProperties'], true);

            $details = array_merge($properties, $details);

            unset($details['memberProperties']);
        }

        return $details;
    }


    public function send_welcome_email()
    {
        if ($this->memberStatus()!='active') return false;

        $API = new PerchAPI(1.0, 'perch_members');

        $Settings = $API->get('Settings');
        $login_page = str_replace('{returnURL}', '', $Settings->get('perch_members_login_page')->val());

        $Email = $API->get('Email');
        $Email->set_template('members/emails/welcome.html');
        $Email->set_bulk($this->to_array());
        $Email->set('login_page', $login_page);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($this->memberEmail());
        $Email->send();

        return true;
    }


    protected function check_email_unique($email)
    {
    	$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE memberEmail='.$this->db->pdb($email).' AND memberID!='.$this->id();
    	$count = $this->db->get_count($sql);

    	if ($count===0) {
    		return true;
    	}

    	return false;
    }




    protected function _email_new_password($clear_pwd)
    {
        $API = new PerchAPI(1.0, 'perch_members');

        $Settings = $API->get('Settings');
        $login_page = str_replace('{returnURL}', '', $Settings->get('perch_members_login_page')->val());

        $Email = $API->get('Email');
        $Email->set_template('members/emails/reset_password.html');
        $Email->set_bulk($this->to_array());
        $Email->set('password', $clear_pwd);
        $Email->set('login_page', $login_page);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($this->memberEmail());
        $Email->send();
    }

    protected function _generate_password($length=8)
    {
        $vals = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $pwd = '';
        for ($i=0; $i<$length; $i++) {
            $pwd .= $vals[rand(0, strlen($vals)-1)];
        }
        return $pwd;
    }

}
