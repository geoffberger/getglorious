jQuery(function() {
	jQuery(".meter > span").each(function() {
		jQuery(this)
			.data("origWidth", jQuery(this).width())
			.width(0)
			.animate({
				width: jQuery(this).data("origWidth")
			}, 1200);
	});
});