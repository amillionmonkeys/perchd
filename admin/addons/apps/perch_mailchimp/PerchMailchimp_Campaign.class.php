<?php

class PerchMailchimp_Campaign extends PerchAPI_Base
{
	protected $table = 'mailchimp_campaigns';
	protected $pk    = 'campaignID';

    public $Template = false;

    private $tmp_url_vars = array();


    public function to_array($template_ids=false)
    {	
    	$out = parent::to_array();

    	if (PerchUtil::count($template_ids) && in_array('campaignURL', $template_ids)) {
            $out['campaignURL'] = $this->campaignURL();
        }

        return $out;
    }

    public function campaignURL()
    {
		$Settings           = PerchSettings::fetch();
		$url_template       = $Settings->get('perch_mailchimp_campaign_url')->val();
		$this->tmp_url_vars = $this->details;
		$out                = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $url_template);
		$this->tmp_url_vars = false;

        return $out;
    }

    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }
}

?>