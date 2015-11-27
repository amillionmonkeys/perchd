<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php 
    $title = perch_pages_title(true);

    perch_layout('header', [
    	'title'=>$title . ' | perch.io',
        'description'=>'A list of fine and friendly folk who build stuff with Perch day-in-day-out'
    ]);
   
    // perch_layout('homepage-header'); 
?>      
        <div class="outer-container">
            <main class="main-body">
                <h2 class="page-title"><?php perch_content('Title'); ?></h2>
                <p>Below you can find a list of fine and friendly folk who build stuff with Perch day-in-day-out. If you're an agency looking for a front-ender to work on well structured client site, or a business looking for a partner on the web: there is a Percher waiting to hear from you!</p>
                <ul class="members">
                    <?php $members = members();

                    foreach ($members as $member){ ?>
                        <li class="member">
                            <div class="member__avatar ">
                                <img src="//www.gravatar.com/avatar/<?php echo md5($member['memberEmail']);?>?s=200&amp;d=mm" width="200" height="200" />
                            </div>
                            <div class="member__content">
                                <div class="byline">
                                    <h3>
                                    <?php
                                    if (isset($member['business']) && $member['business'] == 1){?>
                                         <?php echo isset($member['website']) ? '<a href="'.$member['website'].'" rel="nofollow">'.$member['business_name']. '</a>' : $member['business_name']; ?>
                                    <?php } else { ?>

                                    <?php echo isset($member['website']) ? '<a href="'.$member['website'].'" rel="nofollow">'.$member['first_name']. ' ' . $member['last_name'].'</a>' : $member['first_name']. ' ' . $member['last_name']; ?>
                                    <?php } ?>
                                        <small><?php echo isset($member['country']) ? '<br />' . $member['country'] : ''; ?></small>

                                    </h3>
                                    <p><?php echo isset($member['bio']) ? $member['bio'] : ''; ?></p>
                                    <ul class="tags">
                                    <?php foreach ($member as $key => $value) {
                                        if (strpos($key, 'tag') === 0){
                                            echo '<li>'.$value.'</li>';
                                        }
                                    } ?>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    <?php }?>
                </ul>
                <a class="button" href="/user/register">Get on the list</a>

            </main>
            <?php perch_layout('sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
