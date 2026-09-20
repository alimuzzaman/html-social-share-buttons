<?php
use Mediavine\Grow\Critical_Styles;
use Mediavine\Grow\Frontend_Content;

if ( ! empty( $args['settings']['display']['message'] ) ) { ?>
	<p class="dpsp-share-text <?php echo isset( $args['settings']['display']['show_mobile'] ) ? '' : 'dpsp-hide-on-mobile'; ?>" <?php echo Critical_Styles::get( 'content-share-text', 'content' ); ?>>
		<?php echo esc_html( apply_filters( 'gettext', $args['settings']['display']['message'], $args['settings']['display']['message'], 'social-pug' ) ); ?>
	</p>
	<?php
}
?>
<div id="dpsp-content-<?php echo esc_attr( $args['position'] ); ?>" class="<?php echo esc_attr( $args['wrapper_classes'] ); ?>" <?php echo Critical_Styles::get( 'content-wrapper', 'content' ); ?>>
	<?php echo apply_filters( 'dpsp_prepare_front_end_content', Frontend_Content::compose_buttons( $args['settings'] ) ); ?>
</div>
