<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * SSB Import Export Page Content.
 *
 * @since 2.0.4
 * @version 6.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ssb_settings_api = new Ssb_Settings_Structure();
echo '<div class="wrap">';
if ( class_exists( 'Ssb_Pro_React_Admin' ) || ! class_exists( 'Simple_Social_Buttons_Pro' ) ) {
	Ssb_Settings_Structure::ssb_banner_content();
} else {
	$ssb_settings_api->settings_header();
}
echo '</div>';
?>
<div class="ssb-import-export-page">
	<h2><?php esc_html_e( 'Import/Export Simple Social Share Buttons Settings', 'simple-social-buttons' ); ?></h2>
	<div class=""><?php esc_html_e( 'Import/Export your Social share button Settings for/from other sites.', 'simple-social-buttons' ); ?></div>
	<table class="form-table">
	<tbody>
		<tr class="import_setting">
		<th scope="row">
			<label for="ssb_press_import"><?php esc_html_e( 'Import Settings:', 'simple-social-buttons' ); ?></label>
		</th>
		<td>
			<div class="upload-file"><span>Upload File</span><input type="file" name="ssbImport" id="ssbImport" accept=".json"></div>
			<input type="button" class="button ssb-import" value="<?php esc_html_e( 'Import', 'simple-social-buttons' ); ?>" disabled="disabled">
			<span class="import-sniper">
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>">
			</span>
			<span class="import-text"><?php esc_html_e( 'Simple Social Buttons Share Settings Imported Successfully.', 'simple-social-buttons' ); ?></span>
			<span class="wrong-import"></span>
			<p class="description"><?php esc_html_e( 'Select a file and click on Import to start processing.', 'simple-social-buttons' ); ?></p>
		</td>
		</tr>
		<tr class="export_setting">
		<th scope="row">
			<label for="ssb_configure[export_setting]"><?php esc_html_e( 'Export Settings:', 'simple-social-buttons' ); ?></label>
		</th>
		<td>
			<input type="button" class="button ssb-export" value="<?php esc_html_e( 'Export', 'simple-social-buttons' ); ?>">
			<span class="export-sniper">
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>">
			</span>
			<span class="export-text"><?php esc_html_e( 'Simple Social Button Settings Exported Successfully!', 'simple-social-buttons' ); ?></span>
			<p class="description"><?php esc_html_e( 'Export Simple Social Button Settings.', 'simple-social-buttons' ); ?></p>
		</td>
		</tr>
	</tbody>
	</table>
</div>


<script>
(function($) {
	'use strict';
	$(".import-sniper").hide();
	$(".import-text").hide();
	$(".export-sniper").hide();
	$(".export-text").hide();
	// Remove Disabled attribute from Import Button.
	$( '#ssbImport' ).on( 'change', function( event ) {

	event.preventDefault();

	var ssbFileImp = $( '#ssbImport' ).val();
	$(this).prev('span').html(ssbFileImp.split('\\').pop());
	var ssbpressFileExt = ssbFileImp.substr( ssbFileImp.lastIndexOf('.') + 1 );

	$( '.ssb-import' ).attr( "disabled", "disabled" );

	if ( 'json' === ssbpressFileExt && ssbFileImp.split(/(\\|\/)/g).pop().substring( 0, 3 ) === 'ssb' ) {
		$(".import_setting .wrong-import").html("");
		$( '.ssb-import' ).removeAttr( "disabled" );
	} else {
		$(".import_setting .wrong-import").html("Invalid File.");
	}
	});

	$('.ssb-export').on('click',  function(event) {

	event.preventDefault();

	var dateObj = new Date();
	var month = dateObj.getUTCMonth() + 1; //months from 1-12
	var day = dateObj.getUTCDate();
	var year = dateObj.getUTCFullYear();
	var hours = dateObj.getUTCHours();
	var minutes = dateObj.getUTCMinutes();
	var seconds = dateObj.getUTCSeconds();
	var newdate = year + "-" + month + "-" + day + "_" + hours + "-" + minutes + "-" + seconds;

	$.ajax({

		url: ajaxurl,
		type: 'POST',
		data: {
		action : 'ssb_export',
		security  : '<?php echo esc_html( wp_create_nonce( 'ssb-export-security-check' ) ); ?>'
		},
		beforeSend: function() {
		$(".export_setting .export-sniper").show();
		},
		success: function( response ) {

		$(".export_setting .export-sniper").hide();
		$(".export_setting .export-text").show();

		if ( ! window.navigator.msSaveOrOpenBlob ) { // If msSaveOrOpenBlob() is supported, then so is msSaveBlob().
			$("<a />", {
			"download" : "ssb-export-"+newdate+".json",
			"href" : "data:application/json;charset=utf-8," + encodeURIComponent( response ),
			}).appendTo( "body" )
			.click(function() {
			$(this).remove()
			})[0].click()
		} else {
			var blobObject = new Blob( [response] );
			window.navigator.msSaveBlob( blobObject, "ssb-export-"+newdate+".json" );
		}

		setTimeout(function() {
			$(".export_setting .export-text").fadeOut()
		}, 3000 );
		}
	});
	});

	$('.ssb-import').on( 'click', function(event) {
	event.preventDefault();

	var file    = $('#ssbImport');
	var fileObj = new FormData();
	var content = file[0].files[0];

	fileObj.append( 'file', content );
	fileObj.append( 'action', 'ssb_import' );
	fileObj.append( 'security', '<?php echo esc_html( wp_create_nonce( 'ssb-import-security-check' ) ); ?>' );

	$.ajax({

		processData: false,
		contentType: false,
		url: ajaxurl,
		type: 'POST',
		data: fileObj, // file and action append into variable fileObj.
		beforeSend: function() {
		$(".import_setting .import-sniper").show();
		$(".import_setting .wrong-import").html("");
		$( '.ssb-import' ).attr( "disabled", "disabled" );
		},
		success: function(response) {

		$(".import_setting .import-sniper").hide();
		if ( 'error' === response ) {
			$(".import_setting .wrong-import").html("JSON File is not Valid.");
		} else {
			$(".import_setting .import-text").show();
			setTimeout( function() {
			$(".import_setting .import-text").fadeOut();
			file.val('');
			}, 3000 );
		}

		}
	}); //!ajax.
	});
})(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.


</script>
