<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template for ssb selector.
 *
 * @package ShareThisShareButtons
 */

?>
<p class="st-preview-message ssb-select">
	<?php esc_html_e( 'Preview: for reference only', 'sharethis-share-buttons' ); ?>
</p>
<div class="network-select-type-wrap">
	<h2 style="text-align: center;"><?php esc_html_e( 'Channels', 'sharethis-share-buttons' ); ?></h2>
	<div class="manual-share network-type st-radio-config engage">
		<div class="item">
			<input name="network-select-type engage" class="with-gap" type="radio" value="manual-share" checked="checked" />
			<label>
				<?php esc_html_e( 'Choose Buttons Manually', 'sharethis-share-buttons' ); ?>
			</label>
			<p>
				<?php esc_html_e( 'Select your own social networks and customize', 'sharethis-share-buttons' ); ?>
			</p>
		</div>
	</div>
	<div class="smart-share network-type st-radio-config">
		<div class="item">
			<input name="network-select-type" class="with-gap" type="radio" value="smart-share" />
			<label>
				<?php esc_html_e( 'Smart Share Buttons', 'sharethis-share-buttons' ); ?>
			</label>
		</div>
		<p>
			<?php esc_html_e( 'Automatically selects which social channels to display based on each user’s geolocation and device type', 'sharethis-share-buttons' ); ?>
		</p>
		<label for="social-service-count">
			<?php esc_html_e( 'Select the number or Social Services', 'sharethis-share-buttons' ); ?>
			<select id="social-service-count" name="social-service-count">
				<?php foreach ( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) as $count ) : ?>
					<option value="<?php echo esc_html( $count ); ?>" <?php echo selected( 6, $count ); ?>><?php echo esc_html( $count ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>
</div>
