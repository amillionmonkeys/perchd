<?php
    $HTML = $API->get('HTML');
	$Form = $API->get('Form');

    $message = false;

    $Comments = new PerchComments_Comments;
    include(__DIR__.'/../PerchComments_Akismet.class.php');

    if (!$CurrentUser->has_priv('perch_comments.moderate')) {
        PerchUtil::redirect($API->app_path());
    }

    if (isset($_GET['id']) && $_GET['id']!='') {
        $commentID = (int) $_GET['id'];
        $Comment = $Comments->find($commentID);
        $details = $Comment->to_array();
    }else{
        $message = $HTML->failure_message('Sorry, that comment could not be found.');
    }


    $Template   = $API->get('Template');
    $Template->set('comments/comment.html', 'comments');
    $Form->handle_empty_block_generation($Template);
    $tags = $Template->find_all_tags_and_repeaters();

    $Form->set_required_fields_from_template($Template, $details);

     if ($Form->submitted()) {

        $fixed_fields = $Form->receive(array('commentName','commentEmail', 'commentHTML', 'commentStatus', 'commentDateTime', 'commentURL'));
        $data = $Form->get_posted_content($Template, $Comments, $Comment);
        $data = array_merge($data, $fixed_fields);

        if ($Comment->commentStatus()!=$data['commentStatus']) {
            // status has changed

            $Comment->set_status($data['commentStatus']);
        }

        

        $Comment->update($data);

        if (is_object($Comment)) {
            $message = $HTML->success_message('The comment has been successfully edited.');
        }else{
            $message = $HTML->failure_message('Sorry, that comment could not be edited.');
        }

        if ($Form->submitted_with_add_another()) {
            // find the next unmoderated
            $NextComment = $Comments->get_first_pending($Comment->id());
            if ($NextComment) {
                PerchUtil::redirect($API->app_path().'/edit/?id='.$NextComment->id());
            }else{
                PerchUtil::redirect($API->app_path());
            }
        }



     }

     $details = $Comment->to_array();

?>