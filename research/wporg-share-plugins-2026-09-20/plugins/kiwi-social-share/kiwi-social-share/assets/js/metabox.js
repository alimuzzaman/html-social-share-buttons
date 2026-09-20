(
	function( $ ) {

		// Select / Deselect All toggle in the metabox.
		function kiwiSocialShareSelectToggle() {
			let toggleButton = $( '.kss-checkbox-list' ).find( '.kss-multicheck-toggle' );

			$( toggleButton ).on( 'click', function() {
				let checkBoxes = $( this ).parent( '.kss-toggle-wrapper' );
				let checked    = checkBoxes.parent( 'ul' ).find( '.kss-multicheck' );

				// If the button has already been clicked once.
				if ( toggleButton.data( 'checked' ) ) {
					// Clear the checkboxes and remove the flag.
					checked.prop( 'checked', false );
					toggleButton.data( 'checked', false );
				} else {
					// Otherwise mark the checkboxes and add a flag.
					checked.prop( 'checked', true );
					toggleButton.data( 'checked', true );
				}
			} );
		}

		kiwiSocialShareSelectToggle();

		// Media image upload/select in the metabox.
		function kiwiSocialShareMediaUpload() {
			let file_frame;

			$( document.body ).on(
				'click',
				'.kss-upload-button, .kss-media-item img',
				function( event ) {
					event.preventDefault();

					let element              = $( this );
					let file_target_preview  = element.closest( '.kss-options' ).find( '.kss-file-field-image' );
					let file_target_input    = element.closest( '.kss-options' ).find( '.kss-upload-file' );
					let empty_file_target    = element.closest( '.kss-options' ).find( '.kss-media-status' );
					let file_target_input_id = element.closest( '.kss-options' ).find( '.kss-upload-file-id' );

					file_frame = wp.media.frames.media_file = wp.media(
						{
							title  : kiwiMediaSelects.choose,
							button : {
								text : kiwiMediaSelects.update,
							},
							states : [
								new wp.media.controller.Library(
									{
										title   : kiwiMediaSelects.choose,
										library : wp.media.query(
											{
												type : 'image'
											}
										)
									}
								)
							]
						}
					);

					file_frame.on(
						'select',
						function () {
							let attachment = file_frame.state().get( 'selection' ).first().toJSON();

							file_target_preview.attr( 'src', attachment.url );
							file_target_input.val( attachment.url );
							file_target_input_id.val( attachment.id );

							if (
								file_target_input.length !== 0
								&& file_target_preview.length === 0
								) {
									empty_file_target.append(
										'<div class="kss-media-item">' +
											'<img style="max-width: 350px; width: 100%;" src="' + attachment.url + '"  class="kss-file-field-image" alt="" />' +

											'<p class="kss-remove-wrapper">' +
												'<a href="#" class="kss-remove-file-button">' +
													kiwiMediaSelects.delete +
												'</a>' +
											'</p>' +
										'</div>'
									);
							}
						}
					);

					file_frame.open();
				}
			);
		}

		kiwiSocialShareMediaUpload();

		// Media image remove from the metabox.
		function kiwiSocialShareMediaRemove() {
			$( document.body ).on(
				'click',
				'.kss-remove-file-button',
				function( event ) {
					event.preventDefault();

					let element              = $( this );
					let file_target_preview  = element.closest( '.kss-options' ).find( '.kss-media-item' );
					let file_target_input    = element.closest( '.kss-options' ).find( '.kss-upload-file' );
					let file_target_input_id = element.closest( '.kss-options' ).find( '.kss-upload-file-id' );

					if ( file_target_input ) {
						file_target_input.val('');
						file_target_preview.remove();
						file_target_input_id.val('');
					}
				}
			);
		}

		kiwiSocialShareMediaRemove();

	}
)( jQuery );
