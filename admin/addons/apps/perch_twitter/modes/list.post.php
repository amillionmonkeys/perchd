<?php
    # Side panel
    echo $HTML->side_panel_start();
    
        echo $HTML->para('This page lists all the tweets which have been fetched from Twitter.');
        echo $HTML->para('Click on the Get Tweets button to fetch any new tweets.');
    
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();

    include('_subnav.php');




    if($details['settingTwitterID'] && $details['settingTwitterID']!='') {
        echo $Form->form_start('get-tweets', 'add');
            echo '<div>';
            echo $Form->hidden('settingTwitterID', $details['settingTwitterID']);
            echo $Form->submit('btnSubmit', 'Get Tweets', 'button add');       
            echo '</div>';
        echo $Form->form_end();
    }




    echo $HTML->heading1('Listing Tweets');

 
    if ($message) {
        echo $message;
        $message = '';  
    } 



    /* ----------------------------------------- SMART BAR ----------------------------------------- */
    if (PerchUtil::count($posts)) {
    ?>


    <ul class="smartbar">
        <li class="<?php echo ($filter=='all'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path()); ?>"><?php echo PerchLang::get('All'); ?></a></li>
        <li class="<?php echo ($filter=='type'&&$type=='mine'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path().'?type=mine'); ?>"><?php echo PerchLang::get('Mine'); ?></a></li>
        <li class="<?php echo ($filter=='type'&&$type=='favorites'?'selected':''); ?>"><a href="<?php echo PerchUtil::html($API->app_path().'?type=favorites'); ?>"><?php echo PerchLang::get('Favorites'); ?></a></li>
        <?php

            if ($filter == 'type' && $type == 'mine') {
                $Alert->set('filter', PerchLang::get('You are viewing tweets from your own account.'). ' <a href="'.$API->app_path().'" class="action">'.PerchLang::get('Clear Filter').'</a>');
            }

            if ($filter == 'type' && $type == 'favorites') {
                $Alert->set('filter', PerchLang::get('You are viewing tweets marked as favorites.'). ' <a href="'.$API->app_path().'" class="action">'.PerchLang::get('Clear Filter').'</a>');
            }

        ?>
    </ul>

    <?php
        }

    ?>

     <?php echo $Alert->output(); ?>


    <?php

    /* ----------------------------------------- /SMART BAR ----------------------------------------- */



    
    if (PerchUtil::count($posts)) {
?>   
    <table class="d">
        <thead>
            <tr>
                <th>Tweet</th>
                <th>User</th>
                <th>Account</th>
                <th class="action">Type</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($posts as $Post) {
?>
            <tr>
            	<td class="primary"><?php echo $Post->tweetText(); ?></td>
            	<td>@<?php echo $Post->tweetUser(); ?></td> 
            	<td><?php echo $Post->tweetAccount(); ?></td> 
            	<td><?php echo $Post->tweetType(); ?></td>    
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


    }
    
    echo $HTML->main_panel_end();
?>