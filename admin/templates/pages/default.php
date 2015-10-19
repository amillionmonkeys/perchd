<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php perch_layout('header', [
	'title'=>'Homepage | perch.io'
]);?>

        <div class="hero">
            <div class="outer-container">
                <div class="hero__content">
                    <h2 class="hero__strapline">perchd.io is a place to find <a href="https://grabaperch.com/">Perch</a><br> related tips, tutorials, ideas and inspiration.</h2>
                </div>
                <nav class="icon-nav">
                    <ul>
                        <li><a href="#">
                            <i class="icon-nav__icon"><?php perch_layout('icons/circle-icon--tips')?></i>
                            <h5 class="icon-nav__title">Tutorials, tips &amp; insights ></h5>
                            </a>
                        </li>
                        <li><a href="#">
                            <i class="icon-nav__icon"><?php perch_layout('icons/circle-icon--gallery');?></i>
                            <h5 class="icon-nav__title">Powered by Perch Gallery ></h5>
                            </a>
                        </li>
                        <li><a href="#">
                            <i class="icon-nav__icon"><?php perch_layout('icons/circle-icon--code');?></i>
                            <h5 class="icon-nav__title">Code Snippets ></h5>
                            </a>
                        </li>
                        <li><a href="#">
                            <i class="icon-nav__icon"><?php perch_layout('icons/circle-icon--links');?></i>
                            <h5 class="icon-nav__title">Handy Links ></h5>
                            </a>
                        </li>
                        <li><a href="#">
                            <i class="icon-nav__icon"><?php perch_layout('icons/circle-icon--videos');?></i>
                            <h5 class="icon-nav__title">Videos ></h5>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <div class="outer-container">
            <main class="main-body">
                <h2 class="page-title">Latest Perch tutorials, tips &amp; insights</h2>
                <article class="post">
                    <header class="post__header">
                        <h3 class="post__title"><a href="article.html">News Article</a></h3>
                        <div class="post__byline">
                            <time datetime="2015-09-14">September 14, 2015</time>
                        </div>
                    </header>
                    
                    <div class="post__content">
                        <p>Aliquam id scelerisque massa, vel viverra purus. Donec nisi orci, dignissim in est sit amet, consectetur condimentum magna. Donec fermentum aliquam nisi, in luctus elit consequat eu. Aenean quis ipsum vel urna mollis gravida a a dui. Integer hendrerit quam a ante sagittis, eget tincidunt augue dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce elementum vulputate eleifend.</p>
                    </div>
                    <a href="article.html" class="button">Read more &raquo;</a>  
                </article>
                <hr />

                <article class="post">
                    <header class="post__header">
                        <h3 class="post__title"><a href="article.html">Video</a></h3>
                        <div class="post__byline">
                            <time datetime="2015-09-14">September 14, 2015</time>
                        </div>
                    </header>
                    
                    <div class="post__content">
                        <p>Aliquam id scelerisque massa, vel viverra purus. Donec nisi orci, dignissim in est sit amet, consectetur condimentum magna. Donec fermentum aliquam nisi, in luctus elit consequat eu. Aenean quis ipsum vel urna mollis gravida a a dui. Integer hendrerit quam a ante sagittis, eget tincidunt augue dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce elementum vulputate eleifend.</p>
                    </div>
                    <a href="article.html" class="button">Read more &raquo;</a>  
                </article>
                <hr />

                <article class="post">
                    <header class="post__header">
                        <h3 class="post__title"><a href="article.html">Link to another site</a></h3>
                        <div class="byline">
                            <time datetime="2015-09-14" class="time">September 14, 2015</time>
                        </div>
                    </header>
                    <div class="post__content">
                        <p>Aliquam id scelerisque massa, vel viverra purus. Donec nisi orci, dignissim in est sit amet, consectetur condimentum magna. Donec fermentum aliquam nisi, in luctus elit consequat eu. Aenean quis ipsum vel urna mollis gravida a a dui. Integer hendrerit quam a ante sagittis, eget tincidunt augue dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce elementum vulputate eleifend.</p>
                    </div>
                    <a href="article.html" class="button">Read more &raquo;</a>  
                </article>
                <hr /> 
                <nav class="pagination-nav">
                  <ul class="pagination__list">
                    <li>
                      <a href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>
                    <li><a href="#">1</a></li>
                    <li class="active"><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li>
                      <a href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                  </ul>
                </nav>
            </main>
            <?php perch_layout('sidebar'); ?>
            
        </div>
        
    <?php perch_layout('footer'); ?>
