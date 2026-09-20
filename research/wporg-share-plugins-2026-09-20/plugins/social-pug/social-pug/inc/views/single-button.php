<?php
use Mediavine\Grow\Critical_Styles;

$network_slug_background_color = $args['network_slug'];
if ( $args['network_slug'] == 'twitter' ) :
	$settings = \Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
	$network_slug_background_color = ( empty( $settings['twitter_4ever'] ) ) ? 'x' : $args['network_slug'];
	$args['button_classes'] = ( empty( $settings['twitter_4ever'] ) ) ? str_replace( 'dpsp-twitter', 'dpsp-x', $args['button_classes'] ) : $args['button_classes'];
	if ( $args['network_label'] == 'Twitter' || empty( $args['network_label'] ) ) : // This means the user hasn't customized the label
		$args['network_label'] = ( empty( $settings['twitter_4ever'] ) ) ? $args['network_label'] : 'X';
	endif;
endif;
?>

<li class="dpsp-network-list-item <?php echo 'dpsp-network-list-item-' . $network_slug_background_color; ?>" <?php echo Critical_Styles::get( 'single-button-list-item', $args['location'] ); ?>>
	<?php
	echo'<' . $args['tag'] . ' rel="' . esc_attr( $args['rel'] ) . '" ' . $args['href_attribute']. ' class="' . $args['button_classes'] . '" target="_blank" aria-label="' . esc_attr( $args['title_attribute'] ) . '" title="' . esc_attr( $args['title_attribute'] ) . '" ' . Critical_Styles::get( 'single-button-link', $args['location'] ) . ' >';
	?>
	<span class="dpsp-network-icon <?php echo 'grow' === $args['network_slug'] ? 'dpsp-network-icon-outlined' : ''; ?>"><span class="dpsp-network-icon-inner" <?php echo Critical_Styles::get( 'single-button-icon-inner', $args['location'] ); ?>><?php echo $args['icon_svg']; ?></span></span>
	<?php
	// For all tools except the sidebar, the label span is inside the wrapper tag
	$showLabelsOnMobile = ! $args['show_labels_mobile'] ? ' dpsp-network-hide-label-mobile' : '';
	if ( $args['show_labels'] && 'sidebar' !== $args['location'] ) {
		echo '<span class="dpsp-network-label' . $showLabelsOnMobile . '">' . esc_html( $args['network_label'] ) . '</span>';
	}
	if ( $args['show_share_counts'] ) {
		echo '<span class="dpsp-network-count">' . esc_html( $args['network_shares'] ) . '</span>';
	}
	echo '</' . $args['tag'] . '>';
	if ( $args['show_labels'] && 'sidebar' === $args['location'] ) {
		echo '<span class="dpsp-network-label' . $showLabelsOnMobile . '">' . esc_html( $args['network_label'] ) . '</span>';
	}
	?>
</li>
