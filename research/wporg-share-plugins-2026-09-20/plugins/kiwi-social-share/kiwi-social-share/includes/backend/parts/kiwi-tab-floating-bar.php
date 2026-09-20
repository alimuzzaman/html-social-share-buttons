<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sl-kiwi-tab-floating-bar <?php echo ( $hash === 'sl-kiwi-tab-floating-bar' ) ? 'epsilon-tab-active' : ''; ?>">
    <h2>
        <span><?php echo esc_html__( 'Floating bar', 'kiwi-social-share' ) ?></span>
    </h2>
	<?php
	$floating_panel = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel', '' );
	?>
    <div class="sl-kiwi-opt-group clearfix">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_floating_panel"
                           name="kiwi_general_settings[floating_panel]" <?php echo ! empty( $floating_panel ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Floating bar', 'kiwi-social-share' ) ?>
            </h4>
            <p><?php echo esc_html__( 'You can enable a floating social bar for your website, that can be displayed on the edges of the screen.', 'kiwi-social-share' ) ?></p>
        </div>
		<?php
		$floating_panel_location = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel_location', 'left' );
		?>
        <div class="epsilon-ui-option right clearfix">
            <ul>
                <li>
                    <label>
                        <input type="radio" id="kiwi_floating_panel_location_a"
                               name="kiwi_general_settings[floating_panel_location]"
                               value="left" <?php echo ( $floating_panel_location === 'left' ) ? 'checked' : ''; ?> />
                        <span>
							<i class="icon kiwi-ic-left"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Left', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_floating_panel_location_b"
                               name="kiwi_general_settings[floating_panel_location]"
                               value="bottom" <?php echo ( $floating_panel_location === 'bottom' ) ? 'checked' : ''; ?> />
                        <span>
							<i class="icon kiwi-ic-bottom"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Bottom', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_floating_panel_location_c"
                               name="kiwi_general_settings[floating_panel_location]"
                               value="right" <?php echo ( $floating_panel_location === 'right' ) ? 'checked' : ''; ?> />
                        <span>
							<i class="icon kiwi-ic-right"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Right', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
            </ul>
            <div class="epsilon-ui-overlay <?php echo ! empty( $floating_panel ) ? '' : 'active'; ?>"></div>
        </div>

        <div class="clearfix"></div>
		<?php
		$floating_panel_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel_posttypes', 'all' );
		?>
        <div class="sl-kiwi-opt-description sl-kiwi-radio-post-types sl-kiwi-inner-opt clearfix">
            <h5><?php echo esc_html__( 'Activate this feature on', 'kiwi-social-share' ) ?></h5>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_floating_panel_posttypes_a"
                       name="kiwi_general_settings[floating_panel_posttypes]"
                       value="all" <?php echo ( $floating_panel_posttypes === 'all' ) ? 'checked' : ''; ?> />

                <strong></strong>
				<?php echo esc_html__( 'All Pages', 'kiwi-social-share' ); ?>
            </label>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_floating_panel_posttypes_b"
                       name="kiwi_general_settings[floating_panel_posttypes]"
                       value="custom" <?php echo ( $floating_panel_posttypes === 'custom' ) ? 'checked' : ''; ?>
                />

                <strong></strong>
				<?php echo esc_html__( 'Select post types', 'kiwi-social-share' ); ?>

            </label>

            <div class="epsilon-ui-checklist" <?php echo ( $floating_panel_posttypes === 'all' ) ? 'style="display:none"' : ''; ?>>
                <ul>
					<?php
					$post_types_list_floating = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel_posttypes_list', array(
						'post',
						'page'
					) );
					?>

					<?php foreach ( $post_types as $name => $label ) { ?>
                        <li>
                            <span><?php echo esc_html( $label ) ?></span>
                            <label class="epsilon-ui-checkbox">
                                <input name="kiwi_general_settings[floating_panel_posttypes_list][]"
                                       value="<?php echo esc_attr( $name ); ?>"
									<?php echo ( in_array( $name, $post_types_list_floating ) ) ? 'checked' : ''; ?>
                                       type="checkbox"/>
                                <strong></strong>
                            </label>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div class="epsilon-ui-overlay <?php echo ! empty( $floating_panel ) ? '' : 'active'; ?>"></div>
        </div>
    </div>


    <div class="sl-kiwi-opt-group clearfix">

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Buttons shape', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Change the shape of the social buttons.', 'kiwi-social-share' ) ?></p>
        </div>

		<?php
		$button_shape_floating = Kiwi_Social_Share_Helper::get_setting_value( 'button_shape_floating', 'rect' );
		?>

        <div class="epsilon-ui-option right">
            <ul>
                <li>
                    <label>
                        <input type="radio" id="kiwi_button_shape_floating_a"
                               name="kiwi_general_settings[button_shape_floating]" <?php echo ( $button_shape_floating === 'rect' ) ? 'checked' : ''; ?>
                               value="rect"/>
                        <span>
							<i class="icon kiwi-ic-rect"><strong><em></em><em></em><em></em></strong></i>
							<span><?php echo esc_html__( 'Rect', 'kiwi-social-share' ); ?></span>
						</span>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="kiwi_button_shape_floating_b"
                               name="kiwi_general_settings[button_shape_floating]" <?php echo ( $button_shape_floating === 'pill' ) ? 'checked' : ''; ?>
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

</div>