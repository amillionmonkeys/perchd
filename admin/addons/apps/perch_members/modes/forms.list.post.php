<?php
    # Side panel
    echo $HTML->side_panel_start();

    //echo $HTML->para('');

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');

    


	# Title panel
    echo $HTML->heading1('Listing Member Forms');
    
    if (isset($message)) echo $message;

    if (PerchUtil::count($forms)) {
?>
    <table class="d">
        <thead>
            <tr>
                <th class="first"><?php echo $Lang->get('Form'); ?></th>
                <th><?php echo $Lang->get('Key'); ?></th>
                <th class="action last"></th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($forms as $MemberForm) {
?>
            <tr>
                <td class="primary">
                    <a href="<?php echo $HTML->encode($API->app_path()); ?>/forms/edit/?id=<?php echo $HTML->encode(urlencode($MemberForm->id())); ?>">
                    <?php echo $HTML->encode($MemberForm->formTitle()); ?></a>
                </td>
                <td><?php echo $HTML->encode($MemberForm->formKey()); ?></td>
                <td><a href="<?php echo $HTML->encode($API->app_path()); ?>/forms/delete/?id=<?php echo $HTML->encode(urlencode($MemberForm->id())); ?>" class="delete inline-delete" data-msg="<?php echo $Lang->get('Delete this member?'); ?>"><?php echo $Lang->get('Delete'); ?></a></td>
            </tr>
<?php   
    }
?>
        </tbody>
    </table>
<?php    
        if ($Paging->enabled()) {
            echo $HTML->paging($Paging);
        }
    } // if pages
    
    echo $HTML->main_panel_end();
