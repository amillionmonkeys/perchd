<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
	<title>Perch Example Search Page</title>
	<?php perch_get_css(); ?>
</head>
<body>
	<div class="wrapper cols2-nav-right">
		<div class="primary-content">
		    <?php 
			    perch_content_search(perch_get('q'));
			?>
		</div>
		<nav class="sidebar">
		   <?php perch_search_form(); ?>
    	</nav>
	</div>
    <?php perch_get_javascript(); ?>
</body>
</html>