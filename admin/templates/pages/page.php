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
                <h2 class="page-title"><?php perch_content('Title'); ?></h2>
                <?php perch_content('Main Content'); ?>
            </main>
            <?php perch_layout('sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
