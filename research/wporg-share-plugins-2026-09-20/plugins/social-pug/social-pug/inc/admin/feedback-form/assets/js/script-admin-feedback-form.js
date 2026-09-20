jQuery( function($) {

	function is_email( email ) {
	    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(String(email).toLowerCase());
	}

	var current_panel = 1;

	/**
	 * Trigger feedback form
	 *
	 */
	$(document).on( 'click', '#dpsp-feedback-button', function() {

		$(this).toggleClass('dpsp-inactive');
		$('#dpsp-feedback-form-wrapper').toggleClass('dpsp-inactive');

	});

	/**
	 * Click to select the first options
	 *
	 */
	$(document).on( 'click', '#dpsp-feedback-form-panel-1 .dpsp-selection-label', function() {

		current_panel = 2;

		$('#dpsp-feedback-form-panel-1').removeClass('dpsp-doing').addClass('dpsp-done');
		$('#dpsp-feedback-form-panel-2').removeClass('dpsp-todo').addClass('dpsp-doing');

		$('#dpsp-feedback-form-navigation').fadeIn(250);

		setTimeout( function() {
			$('#dpsp-feedback-form-panel-2 textarea').focus();
		}, 300 );
		
	});

	/**
	 * Handle textarea functionality
	 *
	 */
	$(document).on( 'keyup', '#dpsp-feedback-form-panel-2 textarea', function() {

		var $textarea = $(this);

		$('#dpsp-feedback-form-char-count').html( parseInt( 80 - $textarea.val().length ) );

		/* Show counts */
		if( $textarea.val().length > 0 ) {

			$('#dpsp-feedback-form-description-char-count-1').hide();
			$('#dpsp-feedback-form-description-char-count-2').show();

		} else {

			$('#dpsp-feedback-form-description-char-count-2').hide();
			$('#dpsp-feedback-form-description-char-count-1').show();

		}

		/* Handle next button */
		if( $textarea.val().length >= 80 ) {

			$('#dpsp-feedback-form-next').removeClass('dpsp-inactive');

			$('#dpsp-feedback-form-description-char-count-2').hide();

		} else {

			$('#dpsp-feedback-form-next').addClass('dpsp-inactive');

		}

	});

	/**
	 * Handle email input functionality
	 *
	 */
	$(document).on( 'keyup', '#dpsp-feedback-form-panel-3 input[type=email]', function() {

		var $input = $(this);

		if( is_email( $input.val() ) ) {

			$('#dpsp-feedback-form-send').removeClass('dpsp-inactive');

		} else {

			$('#dpsp-feedback-form-send').addClass('dpsp-inactive');

		}

	});

	/**
	 * Handle back button navigation
	 *
	 */
	$(document).on( 'click', '#dpsp-feedback-form-back', function(e) {

		e.preventDefault();

		$('#dpsp-feedback-form-panel-' + current_panel ).removeClass('dpsp-doing').addClass('dpsp-todo');

		current_panel--;

		$('#dpsp-feedback-form-panel-' + current_panel ).removeClass('dpsp-done').addClass('dpsp-doing');

		if( current_panel == 1 )
			$('#dpsp-feedback-form-navigation').fadeOut(250);

		if( current_panel == 3 ) {
			$('#dpsp-feedback-form-next').hide();
			$('#dpsp-feedback-form-send').show();
		} else {
			$('#dpsp-feedback-form-next').show();
			$('#dpsp-feedback-form-send').hide();
		}

	});


	/**
	 * Handle next button navigation
	 *
	 */
	$(document).on( 'click', '#dpsp-feedback-form-next', function(e) {

		e.preventDefault();

		if( $(this).hasClass('dpsp-inactive') ) {
			$(this).closest('#dpsp-feedback-form-wrapper').find('.dpsp-doing input, .dpsp-doing textarea').focus();
			return false;
		}


		$('#dpsp-feedback-form-panel-' + current_panel ).removeClass('dpsp-doing').addClass('dpsp-done');

		current_panel++;

		$('#dpsp-feedback-form-panel-' + current_panel ).removeClass('dpsp-todo').addClass('dpsp-doing');

		setTimeout( function() {
			$('.dpsp-feedback-form-panel input[type=email]').focus();
		}, 300 );

		if( current_panel == 3 ) {
			$('#dpsp-feedback-form-next').hide();
			$('#dpsp-feedback-form-send').show();
		} else {
			$('#dpsp-feedback-form-next').show();
			$('#dpsp-feedback-form-send').hide();
		}

	});


	/**
	 * Handle send button
	 *
	 */
	$(document).on( 'click', '#dpsp-feedback-form-send', function(e) {

		e.preventDefault();

		if( $(this).hasClass('dpsp-inactive') ) {
			$(this).closest('#dpsp-feedback-form-wrapper').find('.dpsp-doing input, .dpsp-doing textarea').focus();
			return false;
		}

		$('#dpsp-feedback-form-navigation a').fadeOut(250);
		$('#dpsp-feedback-form-navigation .spinner').fadeIn(250);

		var data = {
			action 	   : 'dpsp_ajax_send_feedback',
			dpsp_token  : $('#dpsp_token').val(),
			type       : $('#dpsp-feedback-form-panel-1').find('input[type=radio]:checked').val(),
			message    : $('#dpsp-feedback-form-panel-2').find('textarea').val(),
			user_email : $('#dpsp-feedback-form-panel-3').find('input[type=email]').val()
		}

		$.post( ajaxurl, data, function( response ) {

			$('.dpsp-feedback-form-panel').removeClass('dpsp-doing').fadeOut( 250, function() {
				$('#dpsp-feedback-form-panel-4').removeClass('dpsp-todo').addClass('dpsp-doing').fadeIn();
			});

			$('#dpsp-feedback-form-navigation .spinner').fadeOut(250);

		});

	});

});