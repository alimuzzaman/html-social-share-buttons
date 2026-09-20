<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sl-kiwi-tab-advanced <?php echo ( $hash === 'sl-kiwi-tab-advanced' ) ? 'epsilon-tab-active' : ''; ?>">
    <h2>
        <span><?php echo esc_html__( 'Advanced settings', 'kiwi-social-share' ) ?></span>
    </h2>
	<?php
	$custom_meta_boxes = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes', '' );
	?>
    <div class="sl-kiwi-opt-group clearfix">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_custom_meta_boxes"
                           name="kiwi_general_settings[custom_meta_boxes]" <?php echo ! empty( $custom_meta_boxes ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Custom metaboxes', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Adds Custom Metaboxes for page/post/cpt meta handling', 'kiwi-social-share' ) ?></p>
        </div>

        <div class="clearfix"></div>

		<?php
		$custom_meta_boxes_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes_posttypes', 'all' );
		?>
        <div class="sl-kiwi-opt-description sl-kiwi-radio-post-types sl-kiwi-inner-opt clearfix">
            <h5><?php echo esc_html__( 'Activate this feature on', 'kiwi-social-share' ) ?></h5>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_custom_meta_boxes_posttypes_a"
                       name="kiwi_general_settings[custom_meta_boxes_posttypes]"
                       value="all" <?php echo ( $custom_meta_boxes_posttypes === 'all' ) ? 'checked' : ''; ?> />

                <strong></strong>
				<?php echo esc_html__( 'All Pages', 'kiwi-social-share' ); ?>
            </label>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_custom_meta_boxes_posttypes_b"
                       name="kiwi_general_settings[custom_meta_boxes_posttypes]"
                       value="custom" <?php echo ( $custom_meta_boxes_posttypes === 'custom' ) ? 'checked' : ''; ?>
                />

                <strong></strong>
				<?php echo esc_html__( 'Select post types', 'kiwi-social-share' ); ?>                

            </label>

            <div class="epsilon-ui-checklist" <?php echo ( $custom_meta_boxes_posttypes === 'all' ) ? 'style="display:none"' : ''; ?>>
                <ul>
					<?php
					$post_types_list = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes_posttypes_list', array(
						'post',
						'page'
					) );
					?>
					<?php foreach ( $post_types as $name => $label ) { ?>
                        <li>
                            <span><?php echo esc_html( $label ) ?></span>
                            <label class="epsilon-ui-checkbox">
                                <input name="kiwi_general_settings[custom_meta_boxes_posttypes_list][]"
                                       value="<?php echo esc_attr( $name ); ?>"
									<?php echo ( in_array( $name, $post_types_list ) ) ? 'checked' : ''; ?>
                                       type="checkbox"/>
                                <strong></strong>
                            </label>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div class="epsilon-ui-overlay <?php echo empty( $custom_meta_boxes ) ? 'active' : ''; ?>"></div>
        </div>
    </div>

	<?php
	$click_to_tweet = Kiwi_Social_Share_Helper::get_setting_value( 'click_to_tweet', '' );
	?>
    <div class="sl-kiwi-opt-group clearfix">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_click_to_tweet"
                           name="kiwi_general_settings[click_to_tweet]" <?php echo ! empty( $click_to_tweet ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Click to tweet', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Add a "Click to tweet" button in WordPress editor.', 'kiwi-social-share' ) ?></p>
        </div>

        <div class="clearfix"></div>

		<?php
		$click_to_tweet_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'click_to_tweet_posttypes', 'all' );
		?>
        <div class="sl-kiwi-opt-description sl-kiwi-radio-post-types sl-kiwi-inner-opt clearfix">
            <h5><?php echo esc_html__( 'Activate this feature on', 'kiwi-social-share' ) ?></h5>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_click_to_tweet_posttypes_a"
                       name="kiwi_general_settings[click_to_tweet_posttypes]"
                       value="all" <?php echo ( $click_to_tweet_posttypes === 'all' ) ? 'checked' : ''; ?> />

                <strong></strong>
				<?php echo esc_html__( 'All Pages', 'kiwi-social-share' ); ?>
            </label>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_click_to_tweet_posttypes_b"
                       name="kiwi_general_settings[click_to_tweet_posttypes]"
                       value="custom" <?php echo ( $click_to_tweet_posttypes === 'custom' ) ? 'checked' : ''; ?>
                />

                <strong></strong>
				<?php echo esc_html__( 'Select post types', 'kiwi-social-share' ); ?>

            </label>

            <div
                    class="epsilon-ui-checklist" <?php echo ( $click_to_tweet_posttypes === 'all' ) ? 'style="display:none"' : ''; ?>>
                <ul>
					<?php
					$post_types_list = Kiwi_Social_Share_Helper::get_setting_value( 'click_to_tweet_posttypes_list', array(
						'post',
						'page'
					) );
					?>
					<?php foreach ( $post_types as $name => $label ) { ?>
                        <li>
                            <span><?php echo esc_html( $label ) ?></span>
                            <label class="epsilon-ui-checkbox">
                                <input name="kiwi_general_settings[click_to_tweet_posttypes_list][]"
                                       value="<?php echo esc_attr( $name ); ?>"
									<?php echo ( in_array( $name, $post_types_list ) ) ? 'checked' : ''; ?>
                                       type="checkbox"/>
                                <strong></strong>
                            </label>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div
                    class="epsilon-ui-overlay <?php echo empty( $click_to_tweet ) ? 'active' : ''; ?>"></div>
        </div>
    </div>

	<?php
	$highlight_to_tweet = Kiwi_Social_Share_Helper::get_setting_value( 'highlight_to_tweet', '' );
	?>
    <div class="sl-kiwi-opt-group clearfix">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_highlight_to_tweet"
                           name="kiwi_general_settings[highlight_to_tweet]" <?php echo ! empty( $highlight_to_tweet ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description">
            <h4><?php echo esc_html__( 'Tweet selected text', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Allows tweeting the current selected text in the page.', 'kiwi-social-share' ) ?></p>
        </div>

        <div class="clearfix"></div>
		<?php
		$highlight_to_tweet_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'highlight_to_tweet_posttypes', 'all' );
		?>
        <div class="sl-kiwi-opt-description sl-kiwi-radio-post-types sl-kiwi-inner-opt clearfix">
            <h5><?php echo esc_html__( 'Activate this feature on', 'kiwi-social-share' ) ?></h5>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_highlight_to_tweet_posttypes_a"
                       name="kiwi_general_settings[highlight_to_tweet_posttypes]"
                       value="all" <?php echo ( $highlight_to_tweet_posttypes === 'all' ) ? 'checked' : ''; ?> />

                <strong></strong>
				<?php echo esc_html__( 'All Pages', 'kiwi-social-share' ); ?>
            </label>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_highlight_to_tweet_posttypes_b"
                       name="kiwi_general_settings[highlight_to_tweet_posttypes]"
                       value="custom" <?php echo ( $highlight_to_tweet_posttypes === 'custom' ) ? 'checked' : ''; ?>
                />

                <strong></strong>
				<?php echo esc_html__( 'Select post types', 'kiwi-social-share' ); ?>

            </label>

            <div
                    class="epsilon-ui-checklist" <?php echo ( $highlight_to_tweet_posttypes === 'all' ) ? 'style="display:none"' : ''; ?>>
                <ul>
					<?php
					$post_types_list = Kiwi_Social_Share_Helper::get_setting_value( 'highlight_to_tweet_posttypes_list', array(
						'post',
						'page'
					) );
					?>
					<?php foreach ( $post_types as $name => $label ) { ?>
                        <li>
                            <span><?php echo esc_html( $label ) ?></span>
                            <label class="epsilon-ui-checkbox">
                                <input name="kiwi_general_settings[highlight_to_tweet_posttypes_list][]"
                                       value="<?php echo esc_attr( $name ); ?>"
									<?php echo ( in_array( $name, $post_types_list ) ) ? 'checked' : ''; ?>
                                       type="checkbox"/>
                                <strong></strong>
                            </label>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div
                    class="epsilon-ui-overlay <?php echo empty( $highlight_to_tweet ) ? 'active' : ''; ?>"></div>
        </div>
    </div>
	<?php
	$tracking = Kiwi_Social_Share_Helper::get_setting_value( 'ga_tracking', '', 'kiwi_social_identities' );
	?>
    <div class="sl-kiwi-opt-group clearfix">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_ga_tracking"
                           name="kiwi_social_identities[ga_tracking]" <?php echo ! empty( $tracking ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description fixed">
            <h4><?php echo esc_html__( 'Google Analytics tracking', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Track the click events on your social networks.', 'kiwi-social-share' ) ?></p>
        </div>
    </div>
	<?php
	$advanced_shortcode_manager = Kiwi_Social_Share_Helper::get_setting_value( 'advanced_shortcode_manager', false, 'kiwi_advanced_settings' );
	?>
    <div class="sl-kiwi-opt-group clearfix">
        <div class="sl-kiwi-opt-toggle">
            <div class="epsilon-ui-toggle">
                <label>
                    <input type="checkbox" id="kiwi_advanced_shortcode_manager"
                           name="kiwi_advanced_settings[advanced_shortcode_manager]" <?php echo ! empty( $advanced_shortcode_manager ) ? 'checked' : ''; ?>
                           value="on"/>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="sl-kiwi-opt-description fixed">
            <h4><?php echo esc_html__( 'Shortcode manager', 'kiwi-social-share' ) ?></h4>
            <p><?php echo esc_html__( 'Enable the advanced shortcode manager.', 'kiwi-social-share' ) ?></p>
        </div>
    </div>

</div>