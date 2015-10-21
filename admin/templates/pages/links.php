<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php 
    perch_layout('header', [
    	'title'=>'Handy links from around the web | perch.io'
    ]);
?>      
        <div class="outer-container">
            <main class="main-body">
                <h2 class="page-title">Handy links from around the web</h2>
                <p>It's early days. And we promise, at some stage, the following will be sorted much better, but here is a list of some great Perch resources from around the web. If you've got something to add, email <a href="mailto:hello@perchd.io">hello@perchd.io</a>.</p>
                <?php 

                    $tags = get_tags();

                    echo '<div class="tags">';

                    $active = perch_get('tag') == null ? ' active' : '';

                    echo '<a class="btn--secondary '.$active.'" href="/links">All</a>&nbsp;';

                    foreach ($tags as $tag){
                        $active = perch_get('tag') == $tag ? ' active' : '';

                        echo '<a class="btn--secondary'. $active .'" href="/links?tag='.$tag.'">'.ucfirst(substr($tag, 6)).'</a>&nbsp;';
                    }

                    echo '</div>';

                    $items = pinboard_bookmarks('perch ' . perch_get('tag')); 

                    foreach ($items as $item) {
                        echo '<article class="post--extended">';
                        echo '<header class="post__header"><h3 class="post__title"><a href="'.$item['url'].'">'.$item['title'].'</a></h3>';
                        //echo '<div class="post__byline"><time class="dt-published">'.$item['date'].'</time></div></header>';
                        echo '<div class="post__content"><p>'.$item['description'].'</p></div>';
                        echo '</article>';
                        echo '<hr />';
                    }
                ?>
            </main>
            <?php perch_layout('sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
