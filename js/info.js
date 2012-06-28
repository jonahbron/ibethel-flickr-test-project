/*
 * Control the "About" view toggle link.  When it's clicked, the about info will appear.
 *
 * Copyright 2012 Jonah Dahlquist
 */
$(document).ready(function() {
	$('#info').hide();
	$('#info-control').click(function() {
		$('#info').slideToggle();
		return false;
	});
});
