<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php 
    perch_layout('header', [
    	'title'=>'Homepage | perch.io'
    ]);
   
    // perch_layout('homepage-header'); 
?>      
        <div class="outer-container">
            <main class="main-body">
                <h2 class="page-title">Latest Perch tutorials, tips &amp; insights</h2>
                <?php perch_blog_custom(array(
                    'count' => 10,
                    'template' => 'post_in_list.html',
                    'sort' => 'postDateTime',
                    'sort-order' => 'DESC',
                    'paginate' => true,
                    'page-links' => true,
                    'page-link-style' => 'all',
                )); ?>
            </main>
            <?php perch_layout('sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
