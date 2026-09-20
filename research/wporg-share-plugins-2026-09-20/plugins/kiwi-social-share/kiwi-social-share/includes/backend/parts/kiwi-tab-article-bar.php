<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sl-kiwi-tab-article-bar <?php echo ( $hash === 'sl-kiwi-tab-article-bar' ) ? 'epsilon-tab-active' : ''; ?>">
    <h2>
        <span><?php echo esc_html__( 'Article bar', 'kiwi-social-share' ) ?></span>
    </h2>
    <div class="sl-kiwi-opt-group clearfix">
		<?php
		$share_buttons = Kiwi_Social_Share_Helper::get_setting_value( 'share_buttons', '' );
		?>
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_share_buttons"
                           name="kiwi_general_settings[share_buttons]" <?php echo ! empty( $share_buttons ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description kiwi-title-has-tooltip">
            <h4><?php echo esc_html__( 'Social bar', 'kiwi-social-share' ) ?></h4>
            <div class="kiwi-tooltip-container">
                <span data-attribute="shortcode-tooltip" class="tooltip-opener dashicons dashicons-info"></span>
                <div id="shortcode-tooltip" class="kiwi-notice-container">
                    <p><?php echo esc_html__( 'To insert the share bar anywhere in the article, you can use the [kiwi-social-bar] shortcode. For a more advanced usage of shortcodes, you can activate the Shortcode manager from the Advanced tab.', 'kiwi-social-share' ); ?></p>
                </div>
            </div>
            <p><?php echo esc_html__( 'The social buttons can be shown before or after content (post, page, custom post).', 'kiwi-social-share' ) ?></p>
        </div>

        <div class="epsilon-ui-option right">
			<?php
			$share_buttons_location = Kiwi_Social_Share_Helper::get_setting_value( 'share_buttons_location', 'bottom' );
			?>
            <ul>
                <li>
                    <label>
                        <input type="radio" id="kiwi_share_buttons_location_a"
                               name="kiwi_general_settings[share_buttons_location]" <?php echo ( $share_buttons_location === 'top' ) ? 'checked' : ''; ?>
                               value="top"/>
                        <span>
							<i class="icon kiwi-ic-postop"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Top', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
					<?php

					?>
                    <label>
                        <input type="radio" id="kiwi_share_buttons_location_b"
                               name="kiwi_general_settings[share_buttons_location]"
                               value="bottom" <?php echo ( $share_buttons_location === 'bottom' ) ? 'checked' : ''; ?>/>
                        <span>
							<i class="icon kiwi-ic-posbottom"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Bottom', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_share_buttons_location_c"
                               name="kiwi_general_settings[share_buttons_location]"
                               value="both" <?php echo ( $share_buttons_location === 'both' ) ? 'checked' : ''; ?>/>
                        <span>
							<i class="icon kiwi-ic-posboth"><strong><em class="top"></em><em class="top"></em><em
                                            class="top"></em><em class="bottom"></em><em class="bottom"></em><em
                                            class="bottom"></em></strong></i>
							<span><?php echo esc_html__( 'Both', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
            </ul>

            <div class="epsilon-ui-overlay <?php echo ! empty( $share_buttons ) ? '' : 'active'; ?>"></div>
        </div>

        <div class="clearfix"></div>
		<?php
		$share_buttons_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'share_buttons_posttypes', 'all' );
		?>
        <div class="sl-kiwi-opt-description sl-kiwi-radio-post-types sl-kiwi-inner-opt clearfix">
            <h5><?php echo esc_html__( 'Activate this feature on', 'kiwi-social-share' ) ?></h5>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_share_buttons_posttypes_a"
                       name="kiwi_general_settings[share_buttons_posttypes]"
                       value="all" <?php echo ( $share_buttons_posttypes === 'all' ) ? 'checked' : ''; ?> />

                <strong></strong>
				<?php echo esc_html__( 'All Pages', 'kiwi-social-share' ); ?>
            </label>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_share_buttons_posttypes_b"
                       name="kiwi_general_settings[share_buttons_posttypes]"
                       value="custom" <?php echo ( $share_buttons_posttypes === 'custom' ) ? 'checked' : ''; ?>
                />

                <strong></strong>
				<?php echo esc_html__( 'Select post types', 'kiwi-social-share' ); ?>

            </label>

            <div class="epsilon-ui-checklist" <?php echo ( $share_buttons_posttypes === 'all' ) ? 'style="display:none"' : ''; ?>>
                <ul>
					<?php
					$post_types_list = Kiwi_Social_Share_Helper::get_setting_value( 'share_buttons_posttypes_list', array(
						'post',
						'page'
					) );

					?>
					<?php foreach ( $post_types as $name => $label ) { ?>
                        <li>
                            <span><?php echo esc_html( $label ) ?></span>
                            <label class="epsilon-ui-checkbox">
                                <input name="kiwi_general_settings[share_buttons_posttypes_list][]"
                                       value="<?php echo esc_attr( $name ); ?>"
									<?php echo ( in_array( $name, $post_types_list ) ) ? 'checked' : ''; ?>
                                       type="checkbox"/>
                                <strong></strong>
                            </label>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div class="epsilon-ui-overlay <?php echo ! empty( $share_buttons ) ? '' : 'active'; ?>"></div>
        </div>
    </div>

    <div class="sl-kiwi-opt-group clearfix">

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Buttons shape', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Change the shape of the social buttons.', 'kiwi-social-share' ) ?></p>
        </div>

		<?php
		$button_shape = Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' );
		?>

        <div class="epsilon-ui-option right">
            <ul>
                <li>
                    <label>
                        <input type="radio" id="kiwi_button_shape_a"
                               name="kiwi_general_settings[button_shape]" <?php echo ( $button_shape === 'shift' ) ? 'checked' : ''; ?>
                               value="shift"/>
                        <span>
							<i class="icon kiwi-ic-shift"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Shift', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_button_shape_b"
                               name="kiwi_general_settings[button_shape]" <?php echo ( $button_shape === 'rect' ) ? 'checked' : ''; ?>
                               value="rect"/>
                        <span>
							<i class="icon kiwi-ic-rect"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Rect', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_button_shape_c"
                               name="kiwi_general_settings[button_shape]" <?php echo ( $button_shape === 'leaf' ) ? 'checked' : ''; ?>
                               value="leaf"/>
                        <span>
							<i class="icon kiwi-ic-leaf"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Leaf', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_button_shape_d"
                               name="kiwi_general_settings[button_shape]" <?php echo ( $button_shape === 'pill' ) ? 'checked' : ''; ?>
                               value="pill"/>
                        <span>
							<i class="icon kiwi-ic-pill"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Pill', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>

    <div class="sl-kiwi-opt-group clearfix">

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Button group style', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Change the style of the article bar group.', 'kiwi-social-share' ) ?></p>
        </div>

		<?php
		$article_bar_style = Kiwi_Social_Share_Helper::get_setting_value( 'article_bar_style', 'center' );
		?>

        <div class="epsilon-ui-option right">
            <ul>
                <li>
                    <label>
                        <input type="radio" id="kiwi_article_bar_style_a"
                               name="kiwi_general_settings[article_bar_style]" <?php echo ( $article_bar_style === 'center' ) ? 'checked' : ''; ?>
                               value="center"/>
                        <span>
							<i class="icon kiwi-ic-centered"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Center', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_article_bar_style_b"
                               name="kiwi_general_settings[article_bar_style]" <?php echo ( $article_bar_style === 'fit' ) ? 'checked' : ''; ?>
                               value="fit"/>
                        <span>
							<i class="icon kiwi-ic-filled"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Fit', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>


	<?php
	$share_counts = Kiwi_Social_Share_Helper::get_setting_value( 'share_counts', '' );
	?>
    <div class="sl-kiwi-opt-group">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_share_counts"
                           name="kiwi_general_settings[share_counts]" <?php echo ! empty( $share_counts ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description fixed">
            <h4><?php echo esc_html__( 'Show share counts', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Display the number of counts next to the social network icon.', 'kiwi-social-share' ) ?></p>
        </div>
    </div>

</div>