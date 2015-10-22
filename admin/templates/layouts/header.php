<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php perch_layout_var('title');?></title>
        <meta name="description" content="Tutorials, inspiration, snippets, articles and insights that will help you make great websites with Perch.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/style.css">
        <script>
          (function(d) {
            var config = {
              kitId: 'lea0liy',
              scriptTimeout: 3000,
              async: true
            },
            h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
          })(document);
        </script>
        <script src="/assets/bower_components/modernizr/modernizr.js"></script>
    </head>
    <body>
        <header class="main-header">
            <div class="outer-container">
                <div class="logo">
                    <h1><a href="/">perchd.io</a></h1>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="/">Blog</a></li>
                        <!--<li><a href="#">Gallery</a></li>-->
                        <!--<li><a href="#">Snippets</a></li>-->
                        <li><a href="/links">Links</a></li>
                        <li><a href="/archive/videos">Videos</a></li>
                        <li><a href="/about">About</a></li>
                        <li>
                            <?php perch_search_form(); ?>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>
