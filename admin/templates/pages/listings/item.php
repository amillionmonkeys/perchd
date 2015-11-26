<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php 
    $title = perch_pages_title(true);

    perch_layout('header', [
        'title'=>$title . ' | perch.io'
    ]);
   
    // perch_layout('homepage-header'); 
?>      
        <div class="outer-container">
            <main class="main-body">
                <?php listing(perch_get('s'), array('template'=>'addons_item.html'));?>
            </main>
            <?php perch_layout('user-sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
