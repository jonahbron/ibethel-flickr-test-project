/*
 * Infinite scrolling for Bethel Flickr Test Project
 *
 * This script will automatically load the next set of images when the user
 * scrolls to the bottom of the page.
 *
 * Copyright 2012 Jonah Dahlquist
 */
window.current_page = 1;
$('div.loading').hide();
$(window).scroll(function() {
	if ($(window).scrollTop() == $(document).height() - $(window).height()) {
		window.current_page++;
		$('div.loading').show();
		$.ajax({
			url: 'load_images.php?page=' + window.current_page,
			success: function(html) {
				if (html) {
					$('.center').append(html);
					$('div.loading').hide();
				}
			}
		});
	}
});
