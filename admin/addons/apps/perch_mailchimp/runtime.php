<?php

	PerchSystem::register_search_handler('PerchMailchimp_SearchHandler');

    require('PerchMailchimp_Campaigns.class.php');
    require('PerchMailchimp_Campaign.class.php');
    require('PerchMailchimp_Subscribers.class.php');
    require('PerchMailchimp_Subscriber.class.php');
    require('PerchMailchimp_SearchHandler.class.php');
    require('MailChimp.class.php');


    function perch_mailchimp_form_handler($SubmittedForm)
    {
        if ($SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'perch_mailchimp');
            $Subscribers = new PerchMailchimp_Subscribers($API);
            $Subscribers->subscribe_from_form($SubmittedForm);
        }
        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));
        
    }


    /**
     * Display a form, e.g. a subscribe form
     * @param  [type]  $template [description]
     * @param  array   $content  [description]
     * @param  boolean $return   [description]
     * @return [type]            [description]
     */
    function perch_mailchimp_form($template, $content=array(), $return=false)
    {
        $API      = new PerchAPI(1.0, 'perch_mailchimp');
        $Template = $API->get('Template');
        $Template->set('mailchimp'.DIRECTORY_SEPARATOR.$template, 'mailchimp');
        $html     = $Template->render($content);
        $html     = $Template->apply_runtime_post_processing($html, $content);
        
        if ($return) return $html;
        echo $html;
    }



    /**
    * Get the Campaign Archives
    * 
    * @param bool $return if set to true returns the output rather than echoing it
    */
    function perch_mailchimp_campaigns($opts=array(), $return=false)
    {
        $defaults = array(
            'template'   => 'campaign_list.html',
            'sort'       => 'campaignSendTime',
            'sort-order' => 'DESC',
            'paginate'   => false,
            'count'      => false,
        );

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $API  = new PerchAPI(1.0, 'perch_mailchimp');
        $Campaigns = new PerchMailchimp_Campaigns($API);

        $r = $Campaigns->get_custom($opts);
        
        if ($return) return $r;
        
        echo $r;
    }

    /**
    * Get a single Campaign by slug
    * 
    * @param string slug of the entry
    * @param bool $return if set to true returns the output rather than echoing it
    */
    function perch_mailchimp_campaign_content($slug, $opts=array(), $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_mailchimp');
        $Campaigns = new PerchMailchimp_Campaigns($API);
        $Campaign = $Campaigns->get_one_by('campaignSlug', $slug);

        if ($Campaign) {
            if ($return) return $Campaign->campaignHTML();
            echo $Campaign->campaignHTML();
        }

        return false;
    }

    


?>