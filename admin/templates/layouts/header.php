<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php perch_layout_var('title');?></title>
        <meta name="description" content="Tutorials, inspiration, snippets, articles and insights that will help you make great websites with Perch.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/style.css">
        <script src="https://use.typekit.net/lea0liy.js"></script>
        <script>try{Typekit.load({ async: true });}catch(e){}</script>
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
