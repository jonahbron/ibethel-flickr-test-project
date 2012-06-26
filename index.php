<?php

require 'config.php';
require 'lib/A/Locator.php';
$Locator = new A_Locator();
$Locator->setDir('lib', 'Btp');
$Locator->autoload();

$Requester = new Btp_Flickr_Request_Http(FLICKR_API_KEY, FLICKR_SEARCH_TAGS, FLICKR_SEARCH_PER_PAGE);
$Parser = new Btp_Flickr_Parser($Requester);

$images = $Parser->results();

?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>iBethel Flickr Test Project</title>
		
		<link rel="stylesheet" href="css/style.min.css" />
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
	</head>
	<body>
		<h1>Bethel Flickr Test Project</h1>
		<div class="center">
			<div class="row-fluid">
<?php
$row_length = 0;
foreach ($images as $image) {
	if ($row_length == 3) {
		$row_length = 0;
?>
			</div>
			<div class="row-fluid">
<?php
	}
	$row_length++;
?>
				<div class="span4">
					<a href="<?=$image->link?>"><img class="flickr" src="<?=$image->url?>" alt="<?=$image->title?>" /></a>
				</div>
<?php
}
?>
			</div>
		</div>
	</body>
</html>
