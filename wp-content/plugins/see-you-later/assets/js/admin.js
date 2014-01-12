jQuery(document).ready( function($) {

	// Make sure each heading has a unique ID.
	$( 'ul#settings-sections.subsubsub' ).find( 'a' ).each( function ( i ) {
		var id_value = $( this ).attr( 'href' ).replace( '#', '' );
		$( 'h3:contains("' + $( this ).text() + '")' ).attr( 'id', id_value ).addClass( 'section-heading' );
	});

	$( '#woothemes-syl .subsubsub a.tab' ).click( function ( e ) {
		// Move the "current" CSS class.
		$( this ).parents( '.subsubsub' ).find( '.current' ).removeClass( 'current' );
		$( this ).addClass( 'current' );

		// If "All" is clicked, show all.
		if ( $( this ).hasClass( 'all' ) ) {
			$( '#woothemes-syl h3, #woothemes-syl form p, #woothemes-syl table.form-table, p.submit' ).show();
			return false;
		}

		// If the link is a tab, show only the specified tab.
		var toShow = $( this ).attr( 'href' );

		// Remove the first occurance of # from the selected string (will be added manually below).
		//toShow = toShow.replace( "#+\/", '' );

		$( '#woothemes-syl h3, #woothemes-syl form > p:not(".submit"), #woothemes-syl table' ).hide();
		$( 'h3' + toShow ).show().nextUntil( 'h3.section-heading', 'p, table, table p' ).show();

		return false;
	});

	$('.subsubsub #general').trigger('click');

	// Datepicker
	$( ".date-picker" ).datetimepicker({
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: "both",
		buttonImage: syl_params.calendar_image,
		buttonImageOnly: true,
		minDateTime: 0
	});

	// Datepicker with allow backward
	$( ".date-picker-backward" ).datetimepicker({
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: "both",
		buttonImage: syl_params.calendar_image,
		buttonImageOnly: true
	});

	// Show hide background slideshow options
	$('input[name="woothemes-see-you-later[background_slideshow]"]').change( function() {
		$('#background_slideshow_images').closest('tr').hide();
		$('#background_slideshow_transition').closest('tr').hide();
		$('input[name="woothemes-see-you-later[background_slideshow_randomize]"]').closest('tr').hide();

		if( this.checked ) {
			$('#background_slideshow_images').closest('tr').show();
			$('#background_slideshow_transition').closest('tr').show();
			$('input[name="woothemes-see-you-later[background_slideshow_randomize]"]').closest('tr').show();
		}
	});
	$('input[name="woothemes-see-you-later[background_slideshow]"]').trigger('change');

	// Show Hide newsletter fields based on service
	$('#newsletter_service').change( function() {
		$('#newsletter_service_id').closest('tr').hide();
		$('#newsletter_service_form_action').closest('tr').hide();
		$('#newsletter_mail_chimp_list_subscription_url').closest('tr').hide();
		$('#newsletter_aweber_list_id').closest('tr').hide();
		$('#newsletter_mad_mimi_subscription_url').closest('tr').hide();
		$('#newsletter_wysija_list_id').closest('tr').hide();

		if( $(this).val() == 'aweber' ) {
			$('#newsletter_aweber_list_id').closest('tr').show();
		} else if( $(this).val() == 'campaignmonitor' ) {
			$('#newsletter_service_form_action').closest('tr').show();
		} else if( $(this).val() == 'feedburner' ) {
			$('#newsletter_service_id').closest('tr').show();
		} else if( $(this).val() == 'madmimi' ) {
			$('#newsletter_mad_mimi_subscription_url').closest('tr').show();
		} else if( $(this).val() == 'mailchimp' ) {
			$('#newsletter_mail_chimp_list_subscription_url').closest('tr').show();
		} else if( $(this).val() == 'wysija' ) {
			$('#newsletter_wysija_list_id').closest('tr').show();
		}
	});
	$('#newsletter_service').trigger('change');

	// Load the special URL example
	var access_url = $('input[name="woothemes-see-you-later[access_url]"]').val();
	if ( access_url !== '' ) {
		$('#site-url-example').html(access_url);
	} else {
		$('#site-url-example').html('xyz');
	}

	$('input[name="woothemes-see-you-later[access_url]"]').keyup( function() {
		var access_url = $(this).val();
		if ( access_url !== '' ) {
			$('#site-url-example').html(access_url);
		} else {
			$('#site-url-example').html('xyz');
		}
	});
});