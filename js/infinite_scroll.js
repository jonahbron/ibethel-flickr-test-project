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
