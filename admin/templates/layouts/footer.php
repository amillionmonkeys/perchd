<footer class="main-footer">
            <div class="outer-container">
                <div class="footer__column">
                    <h3>More information</h3>
                    <ul>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/about">About</a></li>
                        <li><a href="/license">License</a></li>
                        <li><a href="/cookies">Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="footer__column">
                    <div id="subscribe-widget" class="widget widget--subscribe">
                        <div class="widget__header">
                            <div class="widget__title"><h3>Subscribe</h3></div>
                        </div>
                        <div class="widget__content">
                            <p>Get a weekly summary of perchd.io articles and news.</p>
                            <?php perch_mailchimp_form('subscribe.html'); ?>
                            
                        </div>
                    </div>
                            
                </div>
                <div class="footer__column">
                    <h3>Conversations</h3>
                    <nav class="icon-nav">
                        <ul>
                            <li><a href="http://twitter.com/perchdio"><?php perch_layout('icons/circle-icon--twitter'); ?></a></li>
                            <li><a href="https://www.youtube.com/channel/UCnXiQiwhLY3wLlBvCsCg3Rg"><?php perch_layout('icons/circle-icon--youtube'); ?></a></li>
                            <li><a href="mailto:hello@perchd.io"><?php perch_layout('icons/circle-icon--mail'); ?></a></li>

                        </ul>
                    </nav>
                </div>

            </div>

            <hr />

            <div class="outer-container">
                <div class="copyright-notice">
                    <p>&copy; 2015 amillionmonkeys Ltd. Site designed by <a href="http://www.sarahevansdesign.co.uk/">Sarah Evans</a> and built by <a href="http://amillionmonkeys.co.uk">amillionmonkeys</a>.<br />
                    Trademarks and brands are the property of their respective owners.</p>
                </div>
            </div>
        </footer>
        <script src="/assets/js/min/scripts-min.js"></script>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-66867813-1', 'auto');
          ga('send', 'pageview');

        </script>
    </body>
</html>
