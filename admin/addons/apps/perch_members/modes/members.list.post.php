<?php
    # Side panel
    echo $HTML->side_panel_start();

    //echo $HTML->para('');

    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
	
	include('_subnav.php');

    echo '<a class="add button" href="'.$HTML->encode($API->app_path().'/edit/').'">'.$Lang->get('Add Member').'</a>';

	# Title panel
    echo $HTML->heading1('Listing Members');
    
    if (isset($message)) echo $message;
?>

    <?php
    /* ----------------------------------------- SMART BAR ----------------------------------------- */
    if (true || PerchUtil::count($members)) {
    ?>


    <ul class="smartbar">
        <li class="<?php echo ($status=='all'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path()); ?>?status=all"><?php echo $Lang->get('All'); ?></a></li>
        <li class="new <?php echo ($status=='pending'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path().'?status=pending'); ?>"><?php echo $Lang->get('Pending'); ?></a></li>
        <?php

            if (PerchUtil::count($tags)) {
                $items = array();
                foreach($tags as $Tag) {
                    $items[] = array(
                            'arg'=>'tag',
                            'val'=>$Tag->tag(),
                            'label'=>$Tag->tagDisplay(),
                            'path'=>$API->app_path()
                        );
                }

                echo PerchUtil::smartbar_filter('cf', 'By Tag', 'Filtered by ‘%s’', $items, 'folder', $Alert, "You are viewing members with tag ‘%s’", $API->app_path());
            }
           
            
        
        ?>
    </ul>

    <?php
        }else{
            $Alert->set('notice', $Lang->get('There are no members yet.'));
        }

    ?>

     <?php echo $Alert->output(); ?>


    <?php

    /* ----------------------------------------- /SMART BAR ----------------------------------------- */
    ?>



<?php    
    if (PerchUtil::count($members)) {

        $cols = $Members->get_edit_columns();
        
        echo '<table class="d itemlist">';
            echo '<thead>';
                echo '<tr>';
                    foreach($cols as $col) {
                        echo '<th>'.PerchUtil::html($col['title']).'</th>';
                    }
                    echo '<th class="last action"></th>';
                echo '</tr>';
            echo '</thead>';
        
            echo '<tbody>';
            $Template = new PerchTemplate;
            $i = 1;
            foreach($members as $Member) {
                $item = $Member->to_array();
                echo '<tr>';
                    $first = true;
                    foreach($cols as $col) {

                        if ($first) { 
                            echo '<td class="primary">';
                            echo '<a href="'.$HTML->encode($API->app_path()).'/edit/?id='.$HTML->encode(urlencode($item['memberID'])).'">';
                        }else{
                            echo '<td>';
                        }

                        if ($col['id']=='_title') {
                            if (isset($item['_title'])) {
                                $title = $item['_title'];
                            }else{
                                $title = PerchLang::get('Item').' '.$i;
                            }
                        }else{
                            if (isset($item[$col['id']])) {
                                $title = $item[$col['id']];    
                            }else{
                                if ($first) {
                                    if (isset($item['_title'])) {
                                        $title = $item['_title'];
                                    }else{
                                        $title = PerchLang::get('Item').' '.$i;
                                    }
                                }else{
                                    $title = '-';
                                }
                            }
                            
                        }

                        if ($col['Tag']) {

                            $FieldType = PerchFieldTypes::get($col['Tag']->type(), false, $col['Tag']);

                            $title = $FieldType->render_admin_listing($title);

                            if ($col['Tag']->format()) {
                                $title = $Template->format_value($col['Tag'], $title);
                            }
                        }
                        
                        if ($first && trim($title)=='') $title = '#'.$item['_id'];

                        echo $title;

                        if ($first) echo '</a>';
                         
                        echo '</td>';

                        $first = false;
                    }
                    echo '<td>';
                        echo '<a href="'.$HTML->encode($API->app_path()).'/delete/?id='.$HTML->encode(urlencode($item['memberID'])).'" class="delete inline-delete">'.PerchLang::get('Delete').'</a>';
                    echo '</td>';
                echo '</tr>';
                $i++;
            }
            echo '</tbody>';
        
        
        echo '</table>';
        
    

  
        if ($Paging->enabled()) {
            echo $HTML->paging($Paging);
        }
    

    } // if pages
    
    echo $HTML->main_panel_end();
