jQuery(document).ready( function($) {
	// Uploading files
	var file_frame;

	$('.st_upload_button').on( 'click', function( event ){
		event.preventDefault();
		var $el = $(this);
		file_path_field = $el.parent().find('.upload-url');

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
		 	button: {
				text: $( this ).data( 'uploader_button_text' ),
		 	},
		  	multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').first().toJSON();

		  // Do something with attachment.id and/or attachment.url here
		  $(file_path_field).val( attachment.url );
		});

		// Finally, open the modal
		file_frame.open();
	});

	// Multi upload, append to textarea
	$('.st_multi_upload_button').on( 'click', function( event ){
		event.preventDefault();
		var $el = $(this);
		file_path_field = $el.parent().find('.upload-urls');

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
		 	button: {
				text: $( this ).data( 'uploader_button_text' ),
		 	},
		  	multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').first().toJSON();

		  // Do something with attachment.id and/or attachment.url here
		  $(file_path_field).append( attachment.url + "\n" );
		});

		// Finally, open the modal
		file_frame.open();
	});
});