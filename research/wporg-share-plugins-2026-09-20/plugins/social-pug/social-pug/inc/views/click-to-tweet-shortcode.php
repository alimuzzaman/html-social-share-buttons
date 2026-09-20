<a class="dpsp-click-to-tweet <?php echo esc_attr( $args['style_class'] ); ?>" href="<?php echo esc_attr( $args['network_share_link'] ); ?>">
	<?php
	if ( $args['avatar'] ) {
		echo $args['avatar'];
	}
	?>
	<span class="dpsp-click-to-tweet-content">
		<?php echo $args['display_tweet']; ?>
	</span>

	<span class="dpsp-click-to-tweet-footer">
		<span class="dpsp-click-to-tweet-cta">
		<?php echo ! empty( $args['settings']['ctt_link_text'] ) ? '<span>' . wp_kses( $args['settings']['ctt_link_text'], \Mediavine\Grow\View_Loader::get_allowed_tags() ) . '</span>' : ''; ?>
		<i class="dpsp-network-btn dpsp-twitter">
		<span class="dpsp-network-icon">
			<span class="dpsp-network-icon-inner">
				<?php echo dpsp_get_svg_icon_output( 'twitter' ); ?>
			</span>
		</span>
		</i>
		</span>
		</span>

</a>
