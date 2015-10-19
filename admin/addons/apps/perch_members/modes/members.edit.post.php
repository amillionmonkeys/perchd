<?php
    # Side panel
    echo $HTML->side_panel_start();   
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start(); 

    include('_subnav.php');

    echo $HTML->heading1($heading1);
    
    if ($message) echo $message;    
    
    
    echo $HTML->heading2('Member details');
    
    
    echo $Form->form_start(false, 'magnetic-save-bar');
    
        //echo $Form->text_field('memberEmail', 'Email', isset($details['memberEmail'])?$details['memberEmail']:false, 'l');


        echo $Form->fields_from_template($Template, $details, array(), false);


        if (!is_object($Member)) {
            echo $Form->text_field('memberPassword', 'Password', isset($details['memberPassword'])?$details['memberPassword']:false, 'l');
        }

        /*
        echo $Form->select_field('memberStatus', 'Status', array(
                array('label'=>$Lang->get('Pending'), 'value'=>'pending'),
                array('label'=>$Lang->get('Active'), 'value'=>'active'),
                array('label'=>$Lang->get('Inactive'), 'value'=>'inactive'),

            ), isset($details['memberStatus'])?$details['memberStatus']:'active');
    */

        $state = '0';
        if (isset($details['memberStatus']) && $details['memberStatus']=='pending') {
            $state = '1';
        }

        echo $Form->hint('Will only be sent to active members');
        echo $Form->checkbox_field('send_email', 'Send welcome email', '1', $state);


        if (is_object($Member)) {

        echo $HTML->heading2('Tags');
?>
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">Enabled</th>
                    <th>Tag</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($tags)) {
                foreach($tags as $Tag) {
                    echo '<tr>';
                        echo '<td class="action">'.$Form->checkbox('tag-'.$Tag->id(), '1', '1').'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tag()).'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tagExpires() ? strftime('%d %b %Y', strtotime($Tag->tagExpires())) : '-').'</td>';
                    echo '</tr>';
                }
            }

            echo '<tr>';
                echo '<td class="action">'.$Form->label('new-tag', PerchLang::get('New')).'</td>';
                echo '<td>'.$Form->text('new-tag', false).'</td>';
                echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>


<?php
        }// is object Member

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    
    echo $Form->form_end();
    
    echo $HTML->main_panel_end();
