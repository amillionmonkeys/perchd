<?php
    include('Listings.class.php');
    include('Listing.class.php');


    function listing_form_handler($SubmittedForm)
    {
        if ($SubmittedForm->formID=='listing' && $SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'listing');
            $Listings = new Listings($API);
            $Listings->receive_new_listing($SubmittedForm);
        }


        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));
    }


    function listing($listingSlug, $opts=false, $return=false)
    {
        $API  = new PerchAPI(1.0, 'listing');

        $defaults = array();
        $defaults['template'] = 'listing.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Listings = new Listings($API);

        $Listing = $Listings->find_with_status($listingSlug, 'LIVE');

        if (is_object($Listing)) {
            $Template = $API->get('Template');
            $Template->set('listing/'.$opts['template'], 'listing');

            $r = $Template->render($Listing);
            $r = $Template->apply_runtime_post_processing($r);

            if ($return) return $r;

            echo $r;

        }

        return false;
    }

    /**
     * Get the comments for a specific item
     * @param  string  $parentID   ID or slug for the post
     * @param  array $opts=false   Options
     * @param  boolean $return=false Return or output
     */
    function listings($listingType, $opts=false, $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_comments');

        $defaults = array();
        $defaults['template']        = 'comment.html';
        $defaults['count']           = false;
        $defaults['sort']            = 'commentDateTime';
        $defaults['sort-order']      = 'ASC';
        $defaults['paginate']        = false;
        $defaults['pagination-var']  = 'comments';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Listings = new Listings($API);

        $r = $Listings->get_custom($listingType, $opts);

        if ($return) return $r;

        echo $r;
    }

    function listings_count($parentID, $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_comments');
        $Comments = new PerchComments_Comments($API);

        $r = $Comments->get_count_for_parent($parentID, 'LIVE');

        if ($return) return $r;
        echo $r;
    }

    function listings_form($listingType, $opts=false, $return=false)
    {
        $API  = new PerchAPI(1.0, 'listing');

        $defaults = array();
        $defaults['template']        = 'listings_form.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Template = $API->get('Template');
        $Template->set('listings/'.$opts['template'], 'listing');
        $html = $Template->render(array(
                'memberID'=>perch_member_get('memberID'),
                ));

        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;
    }


