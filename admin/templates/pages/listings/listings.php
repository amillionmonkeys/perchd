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
                <h1>Addons</h1>
                <?php listings('listing', array('template'=>'addons_listing.html'));?>
                <a class="button" href="/user/apps/new">Submit a new app</a>
            </main>
            <?php perch_layout('user-sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
