<?php perch_layout('header', [
    'title'=>'Homepage | perch.io'
]);?>
        
    <div class="outer-container">
        <main class="main-body">
            <article class="post--extended">            
                <?php perch_blog_post(perch_get('s')); ?>
                <?php perch_blog_author_for_post(perch_get('s')); ?>
                <?php perch_blog_post_comments(perch_get('s')); ?>
                <?php perch_blog_post_comment_form(perch_get('s')); ?>
            </article>
        </main>
        <?php perch_layout('sidebar'); ?>
        
    </div>
    
<?php perch_layout('footer'); ?>