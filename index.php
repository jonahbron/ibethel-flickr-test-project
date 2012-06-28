<!DOCTYPE html>
<html lang="en">
	<head>
		<title>iBethel Flickr Test Project</title>
		
		<link rel="stylesheet" href="css/style.min.css" />
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />

		<script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script src="js/infinite_scroll.js"></script>
	</head>
	<body>
		<h1>Bethel Flickr Test Project</h1>
		<h3>&copy; Copyright Jonah Dahlquist 2012</h3>
		<div class="center" id="center">
<?php
$page = 1;
require_once 'list_images.php';
?>
		</div>
		<div class="loading">Loading next images...</div>
	</body>
</html>
