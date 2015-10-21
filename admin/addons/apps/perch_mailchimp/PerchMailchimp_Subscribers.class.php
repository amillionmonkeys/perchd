<?php

class PerchMailchimp_Subscribers extends PerchAPI_Factory
{
    protected $table     = 'mailchimp_subscribers';
	protected $pk        = 'subscriberID';
	protected $singular_classname = 'PerchMailchimp_Subscriber';
	
	protected $default_sort_column = 'subscriberID';
	

    public function subscribe_from_form($Form)
    {
        $Settings = $this->api->get('Settings');
        $api_key  = $Settings->get('perch_mailchimp_api_key')->settingValue();
        $list_id  = $Settings->get('perch_mailchimp_list_id')->settingValue();

        $merge_vars = array();
        $groupings  = array();
        $confirmed  = false;

        $double_optin      = true;
        $send_welcome      = true;
        $update_existing   = true;
        $replace_interests = false;

        $FormTag = $Form->get_form_attributes();

        if ($FormTag->is_set('double_optin')) {
            $double_optin = $FormTag->double_optin();
        }

        if ($FormTag->is_set('send_welcome')) {
            $send_welcome = $FormTag->send_welcome();
        }

        $attr_map = $Form->get_attribute_map('mailer');
        if (PerchUtil::count($attr_map)) {
            foreach($attr_map as $fieldID=>$merge_var) {
                switch($merge_var) {
                    case 'email':
                        $email = $Form->data[$fieldID];
                        break;

                    case 'confirm_subscribe':
                        $confirmed = PerchUtil::bool_val($Form->data[$fieldID]);
                        break;

                    default:
                        $merge_vars[$merge_var] = $Form->data[$fieldID];
                        break;

                }
            }
        }

        if ($confirmed) {
            $MailChimp = new MailChimp($api_key);

            $result = $MailChimp->call('lists/subscribe', array(
                    'id'                => $list_id,
                    'email'             => array('email'=>$email),
                    'merge_vars'        => $merge_vars,
                    'double_optin'      => $double_optin,
                    'update_existing'   => $update_existing,
                    'replace_interests' => $replace_interests,
                    'send_welcome'      => $send_welcome,
                ));

            return $result;
        }

        return false;
    }


	public function all_members($Paging=false)
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
}
?>