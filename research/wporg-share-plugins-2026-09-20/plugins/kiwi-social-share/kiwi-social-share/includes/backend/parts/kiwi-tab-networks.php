<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="sl-kiwi-tab-networks <?php echo ( $hash === 'sl-kiwi-tab-networks' || empty( $hash ) ) ? 'epsilon-tab-active' : ''; ?>">
    <h2>
        <span><?php echo esc_html__( 'Networks & Colors', 'kiwi-social-share' ) ?></span>
    </h2>
	<?php
	$styles_color = Kiwi_Social_Share_Helper::get_setting_value( 'styles_colors', 'original' );
	?>
    <div class="clearfix">
        <label class="epsilon-ui-radio epsilon-ui-radio-toggle ui-radio-inline">
            <input type="radio" id="kiwi_styles_colors_a"
                   name="kiwi_general_settings[styles_colors]" <?php echo ( $styles_color === 'original' ) ? 'checked' : ''; ?>
                   value="original"/>

            <strong></strong>
			<?php echo esc_html__( 'Original', 'kiwi-social-share' ); ?>
        </label>

        <label class="epsilon-ui-radio epsilon-ui-radio-toggle ui-radio-inline">
            <input type="radio" id="kiwi_styles_colors_b"
                   name="kiwi_general_settings[styles_colors]"
                   <?php echo ( $styles_color === 'monochrome' ) ? 'checked' : ''; ?>
                   value="monochrome"/>

            <strong></strong>
			<?php echo esc_html__( 'Monochrome', 'kiwi-social-share' ); ?>

        </label>

        <label class="epsilon-ui-radio epsilon-ui-radio-toggle ui-radio-inline">
            <input type="radio" id="kiwi_styles_colors_c"
                   name="kiwi_general_settings[styles_colors]"
                   <?php echo ( $styles_color === 'custom' ) ? 'checked' : ''; ?>
                   value="custom"/>

            <strong></strong>
			<?php echo esc_html__( 'Custom', 'kiwi-social-share' ); ?>

        </label>
    </div>

    <div class="sl-kiwi-networks kiwi-styles">

        <h2>
            <span><?php echo esc_html__( 'Social Network', 'kiwi-social-share' ) ?></span>
            <span><?php echo esc_html__( 'Background', 'kiwi-social-share' ) ?></span>
            <span><?php echo esc_html__( 'Text color', 'kiwi-social-share' ) ?></span>
            <span><?php echo esc_html__( 'Hover Background', 'kiwi-social-share' ) ?></span>
            <span><?php echo esc_html__( 'Hover Text color', 'kiwi-social-share' ) ?></span>
            <span><?php echo esc_html__( 'Social bar', 'kiwi-social-share' ) ?>
                <label class="epsilon-ui-checkbox"><input id="social-bar-all" type="checkbox"/>
                    <strong class="goleft"></strong></label></span>
            <span><?php echo esc_html__( 'Floating bar', 'kiwi-social-share' ) ?>
                <label class="epsilon-ui-checkbox"><input id="floating-bar-all" type="checkbox"/>
                    <strong style="transform: translateX(-7px);"></strong></label></span>
        </h2>

        <ul>
			<?php foreach ( $colors as $network => $props ) { ?>
				<?php if ( $network === 'monochrome' || ! isset( $kiwi_networks[$network]['id'] ) ) {
					continue;
				} ?>
                <li data-network="<?php echo esc_attr( $kiwi_networks[ $network ]['id'] ) ?>"
                    class="epsilon-custom-colors">
					<span>
						<strong class="kiwi-nw-<?php echo esc_attr( $kiwi_networks[ $network ]['id'] ) ?>">
							<i class="kicon-<?php echo esc_attr( $kiwi_networks[ $network ]['icon'] ) ?>"
                               aria-hidden="true"></i>
						</strong>
						<?php echo esc_html( $kiwi_networks[ $network ]['label'] ) ?>
					</span>
                    <span>
						<span class="epsilon-ui-color" data-prop="background">
							<input data-color-original="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_network_colors( $kiwi_networks[ $network ]['id'], 'background' ) ) ?>"
                                   data-color-custom="<?php echo esc_attr( $props['background'] ); ?>"
                                   data-color-monochrome="<?php echo esc_attr( $colors['monochrome']['background'] ); ?>"
                                   name="kiwi_network_colors[<?php echo esc_attr( $network ) ?>][background]"
                                   value="<?php echo esc_attr( $props['background'] ); ?>"/>
							<em><?php echo esc_html( $props['background'] ); ?></em>
						</span>
					</span>

                    <span>
						<span class="epsilon-ui-color" data-prop="text">
							<input
                                    data-color-original="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_network_colors( $kiwi_networks[ $network ]['id'], 'text' ) ) ?>"
                                    data-color-custom="<?php echo esc_attr( $props['text'] ); ?>"
                                    data-color-monochrome="<?php echo esc_attr( $colors['monochrome']['text'] ); ?>"
                                    name="kiwi_network_colors[<?php echo esc_attr( $network ) ?>][text]"
                                    value="<?php echo esc_attr( $props['text'] ); ?>"/>
							<em><?php echo esc_html( $props['text'] ); ?></em>
						</span>
					</span>

                    <span>
						<span class="epsilon-ui-color" data-prop="hover_background">
							<input
                                    data-color-original="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_network_colors( $kiwi_networks[ $network ]['id'], 'hover_background' ) ) ?>"
                                    data-color-custom="<?php echo esc_attr( $props['hover_background'] ); ?>"
                                    name="kiwi_network_colors[<?php echo esc_attr( $network ) ?>][hover_background]"
                                    data-color-monochrome="<?php echo esc_attr( $colors['monochrome']['hover_background'] ); ?>"
                                    value="<?php echo esc_attr( $props['hover_background'] ); ?>"/>
							<em><?php echo esc_html( $props['hover_background'] ); ?></em>
						</span>
					</span>

                    <span>
						<span class="epsilon-ui-color" data-prop="hover_text">
							<input
                                    data-color-original="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_network_colors( $kiwi_networks[ $network ]['id'], 'hover_text' ) ) ?>"
                                    data-color-custom="<?php echo esc_attr( $props['hover_text'] ) ?>"
                                    name="kiwi_network_colors[<?php echo esc_attr( $network ) ?>][hover_text]"
                                    data-color-monochrome="<?php echo esc_attr( $colors['monochrome']['hover_text'] ); ?>"
                                    value="<?php echo esc_attr( $props['hover_text'] ) ?>"/>
							<em><?php echo esc_html( $props['hover_text'] ) ?></em>
						</span>
					</span>

                    <span>
						<?php if ( ! $networks[ $network ]['locked'] ): ?>
                            <label class="epsilon-ui-checkbox">
							<input name="kiwi_general_settings[networks_article_bar][]"
                                   value="<?php echo esc_attr( $network ); ?>"
                                   data-list-item="kiwi-nw-<?php echo esc_attr( $network ) ?>"
                                   data-icon="kicon-<?php echo esc_attr( $network ) ?>"
                                   data-number="<?php echo absint( $networks[ $network ]['count'] ) ?>"
                                   data-source="article-bar" <?php echo in_array( 'article-bar', $networks[ $network ]['checked'] ) ? 'checked' : '' ?>
                                   type="checkbox"/>
							<strong></strong>
						</label>
						<?php endif; ?>
					</span>

                    <span>
					<?php if ( ! $networks[ $network ]['locked'] ): ?>
                        <label class="epsilon-ui-checkbox">
							<input name="kiwi_general_settings[networks_floating_bar][]"
                                   value="<?php echo esc_attr( $network ); ?>"
                                   data-list-item="kiwi-nw-<?php echo esc_attr( $network ) ?>"
                                   data-icon="kicon-<?php echo esc_attr( $network ) ?>"
                                   data-number="<?php echo absint( $networks[ $network ]['count'] ) ?>"
                                   data-source="floating-bar" <?php echo in_array( 'floating-bar', $networks[ $network ]['checked'] ) ? 'checked' : '' ?>
                                   type="checkbox"/>
							<strong></strong>
						</label>    
					<?php endif; ?>
					</span>

                </li>
			<?php } ?>
        </ul>
        <div
                class="epsilon-ui-overlay networks <?php echo ( $styles_color === 'original' ) ? 'active' : ''; ?>"></div>
    </div>

</div>
