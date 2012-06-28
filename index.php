<?php
/*
 * Main point of entry for Bethel Flickr Test Project.
 *
 * Uses list_images.php to load the first set of photos from Flickr.
 *
 * Copyright 2012 Jonah Dahlquist
 */
?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>iBethel Flickr Test Project</title>
		
		<link rel="stylesheet" href="css/style.min.css" />
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />

		<script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script src="js/infinite_scroll.js"></script>
		<script src="js/info.js"></script>
	</head>
	<body>
		<h1>Bethel Flickr Test Project</h1>
		<p><a href="#" id="info-control">About</a></p>
		<section id="info">
			<p>This PHP-based application loads images from the Flickr API with the tags <em>"bethel"</em> or <em>"bethel church"</em>.  The initial set of thirty-three images is outputted by PHP when the page is loaded.  The images are rendered in a grid using the Twitter Bootstrap fluid grid system, and the stylesheets are compiled with LESS.</p>
			<p>To display more than the originally loaded thirty-three images, the now popular "infinite-scrolling" technique has been implemented.  When the viewer scrolls to the bottom, a small message notifies them that there are more images loading, until they have loaded and are shown.  jQuery was chosen as the framework to use because of Twitter Bootstrap's integration with it.</p>
			<p>To solve the problem of duplicate rendering code, the same script is called by both main PHP script, and the Ajax call by Javascript.  The only difference is the point-of-entry.</p>
			<p>API data loading is handled by two classes: the Requester, and the Parser.  The Requester handles the cache, and the remote API, while the Parser is only responsible for reading the data.  This makes it easy to keep the program oblivious to the fact that caching is being utilised.  Both of these classes are unit tested.</p>
			<p>&copy; Copyright Jonah Dahlquist 2012</p>
		</section>
		<div class="center" id="center">
<?php
$page = 1;
require_once 'list_images.php';
?>
		</div>
		<div class="loading">Loading next images...</div>
	</body>
</html>
