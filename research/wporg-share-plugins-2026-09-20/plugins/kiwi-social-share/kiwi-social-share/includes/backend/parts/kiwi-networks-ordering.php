<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="sl-kiwi-dragdrop">
    <span class="suggestions"><?php echo esc_html__( 'Drag & drop elements to reorder', 'kiwi-social-share' ); ?></span>
    <div>
        <ul data-id="networks_ordering" data-style="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'article_bar_style', 'center' ) ) ?>"
            class="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' ) ) ?>">
			<?php foreach ( $networks as $network ) { ?>
				<?php
				if ( ! isset( $network['locked'] ) || $network['locked'] ) {
					continue;
				}
				?>

				<?php
				$share_counts = Kiwi_Social_Share_Helper::get_setting_value( 'share_counts', '' );
				?>
                <li data-item="<?php echo esc_attr( $network['name'] ) ?>"
                    class="sl-kiwi-item-<?php echo in_array( 'article-bar', $network['checked'] ) ? 'add' : 'remove'; ?>">
                    <a href="#" class="kiwi-nw-<?php echo esc_attr( $network['name'] ) ?>">
						<span>
							<i class="kicon-<?php echo esc_attr( $kiwi_networks[ $network['name'] ]['icon'] ) ?>"
                               aria-hidden="true"></i> <?php echo ( ! empty( $share_counts ) && $network['count'] > 0 ) ? esc_attr( $network['count'] ) : '' ?>
						</span>
                    </a>
                </li>
			<?php } ?>
        </ul>

        <ul data-id="networks_ordering_floating_bar" class="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'button_shape_floating', 'rect' ) ) ?>">
		    <?php foreach ( $networks as $network ) { ?>
			    <?php
			    if ( ! isset( $network['locked'] ) || $network['locked'] ) {
				    continue;
			    }
			    ?>

                <li data-item="<?php echo esc_attr( $network['name'] ) ?>"
                    class="sl-kiwi-item-<?php echo in_array( 'floating-bar', $network['checked'] ) ? 'add' : 'remove'; ?>">
                    <a href="#" class="kiwi-nw-<?php echo esc_attr( $network['name'] ) ?>">
						<span>
							<i class="kicon-<?php echo esc_attr( $kiwi_networks[ $network['name'] ]['icon'] ) ?>"
                               aria-hidden="true"></i>
						</span>
                    </a>
                </li>
		    <?php } ?>
        </ul>
    </div>
    <input type="hidden" id="kiwi_networks_ordering" name="kiwi_general_settings[networks_ordering]"
           value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'networks_ordering', '' ) ) ?>"/>
</div>