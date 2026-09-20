<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tabs for product select.
 *
 * @package ShareThisShareButtons
 */

?>
<div class="button-tab inline engage">
	<img src="<?php echo esc_url( $this->plugin->dir_url ) . '/assets/inline-share-buttons.svg'; ?>">
	<?php esc_html_e( 'Inline Share Buttons', 'sharethis-share-buttons' ); ?>
	<span><?php echo esc_html( $enabled['inline'] ); ?></span>
</div>
<div class="button-tab sticky">
	<img src="<?php echo esc_url( $this->plugin->dir_url ) . '/assets/sticky-share-buttons.svg'; ?>">
	<?php esc_html_e( 'Sticky Share Buttons', 'sharethis-share-buttons' ); ?>
	<span><?php echo esc_html( $enabled['sticky'] ); ?></span>
</div>
<div class="button-tab gdpr">
	<img src="<?php echo esc_url( $this->plugin->dir_url ) . '/assets/consent-management-platform.svg'; ?>">
	<?php esc_html_e( 'Consent Management Platform', 'sharethis-share-buttons' ); ?>
	<span><?php echo esc_html( $enabled['gdpr'] ); ?></span>
</div>
