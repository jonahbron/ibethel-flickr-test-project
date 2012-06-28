<?php
/*
 * Fetch and render a specific page of the image set with list_images.php
 *
 * Copyright 2012 Jonah Dahlquist
 */

if (isset($_GET['page'])) {
	$page = (int) $_GET['page'];

	require_once 'list_images.php';
}

?>
