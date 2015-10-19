<?php
    
    $HTML = $API->get('HTML');
    
    
    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);
    
    $Comments = new PerchComments_Comments($API);


	$Form = $API->get('Form');

    if ($Form->posted() && $Form->validate()) {

        $comments = $Form->find_items('comment-', true);
        if (PerchUtil::count($comments)) {
            $status = $_POST['commentStatus'];
            foreach($comments as $commentID) {
                $Comment = $Comments->find($commentID);
                $Comment->set_status($status);
            }


        }

    }


	
	$pending_comment_count = $Comments->get_count('PENDING');

    $comments = array();
	
	$status = 'pending';

    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = $_GET['status'];
    }
    
    $comments = $Comments->get_by_status($status, $Paging);


    if ($comments == false) {
        $Comments->attempt_install();
    }
 
?>