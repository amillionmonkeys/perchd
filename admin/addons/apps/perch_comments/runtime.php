<?php
    require('PerchComments_Comments.class.php');
    require('PerchComments_Comment.class.php');


    function perch_comments_form_handler($SubmittedForm)
    {
        if ($SubmittedForm->formID=='comment' && $SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'perch_comments');
            $Comments = new PerchComments_Comments($API);
            $Comments->receive_new_comment($SubmittedForm);
        }

        if ($SubmittedForm->formID=='vote' && $SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'perch_comments');
            $Comments = new PerchComments_Comments($API);
            $Comments->receive_new_vote($SubmittedForm);
        }


        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));
    }


    function perch_comment($commentID, $opts=false, $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_comments');

        $defaults = array();
        $defaults['template']        = 'comment.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Comments = new PerchComments_Comments($API);

        $Comment = $Comments->find_with_status($commentID, 'LIVE');

        if (is_object($Comment)) {
            $Template = $API->get('Template');
            $Template->set('comments/'.$opts['template'], 'comments');

            $r = $Template->render($Comment);
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
    function perch_comments($parentID, $opts=false, $return=false)
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

        $Comments = new PerchComments_Comments($API);

        $r = $Comments->get_custom($parentID, $opts);

        if ($return) return $r;

        echo $r;
    }

    function perch_comments_count($parentID, $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_comments');
        $Comments = new PerchComments_Comments($API);

        $r = $Comments->get_count_for_parent($parentID, 'LIVE');

        if ($return) return $r;
        echo $r;
    }

    function perch_comments_form($parentID, $parentTitle=false, $opts=false, $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_comments');

        $defaults = array();
        $defaults['template']        = 'comment_form.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if ($parentTitle==false) {
            $parentTitle = perch_pages_title(true);
        }

        $Template = $API->get('Template');
        $Template->set('comments/'.$opts['template'], 'comments');
        $html = $Template->render(array(
                'parentID'=>$parentID,
                'parentTitle'=>PerchUtil::html($parentTitle, true)
                ));
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;
    }

    function perch_comments_delete_old_spam($days)
    {
        $API  = new PerchAPI(1.0, 'perch_comments');
        $Comments = new PerchComments_Comments($API);
        return $Comments->delete_old_spam($days);
    }

