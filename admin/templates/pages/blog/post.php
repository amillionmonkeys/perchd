<?php 
    
    perch_blog_check_preview();
    
    $post = perch_blog_post(perch_get('s'), true);

    if(!$post) header('Location: /errors/404');

    $title = perch_blog_post_field(perch_get('s'), 'postTitle', true);
    
    perch_layout('header', [
        'title'=>$title . ' | perchd.io',
        'blog-post' => true
    ]);
?>
        
    <div class="outer-container">
        <main class="main-body">
            <article class="post--extended">            
                <?php 

                if($post) {
                    echo $post;
                    perch_blog_post_comments(perch_get('s'));
                    perch_blog_post_comment_form(perch_get('s'));
                }?>
            </article>
        </main>
        <?php perch_layout('sidebar'); ?>
    </div>
    
<?php perch_layout('footer'); ?>