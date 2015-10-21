<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php 
    perch_layout('header', [
    	'title'=>'Search Results | perch.io'
    ]);
   
    // perch_layout('homepage-header'); 
?>      
        <div class="outer-container">
            <main class="main-body">
                <?php 
				    perch_content_search(perch_get('q'));
				?>

            <?php perch_layout('sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>