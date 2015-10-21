<aside class="sidebar">
    <div class="widget widget--twitter">
        <div class="widget__header">
            <div class="widget__title"><h3><?php perch_layout('icons/icon--twitter')?> Twitter</div>
        </div>
        <div class="widget__content">
        <?php perch_twitter_get_latest(); ?>
        </div>
    </div>

    <div class="widget widget--subscribe">
        <div class="widget__header">
            <div class="widget__title"><h3><?php perch_layout('icons/icon--rss')?> Subscribe</h3></div>
        </div>
        <div class="widget__content">
            <p>Get a weekly summary of perchd.io articles and news.</p>
            <?php perch_mailchimp_form('subscribe.html'); ?>
        </div>
    </div>

</aside>