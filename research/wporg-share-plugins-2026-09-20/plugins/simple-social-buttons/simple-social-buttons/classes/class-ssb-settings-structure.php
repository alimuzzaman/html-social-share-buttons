<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Settings structure class
 *
 * Handles the settings sections and fields structure for Simple Social Buttons.
 *
 * @package SimpleSocialButtons
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ssb_Settings_Structure' ) ) :
	/**
	 * Settings structure class.
	 *
	 * @since 1.0.0
	 */
	class Ssb_Settings_Structure {

		/**
		 * Settings sections array.
		 *
		 * @var array
		 */
		protected $settings_sections = array();

		/**
		 * Settings fields array
		 *
		 * @var array
		 */
		protected $settings_fields = array();

		/**
		 * Constructor method.
		 *
		 * Enqueues scripts and styles, and adds an AJAX action to activate the plugin.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'ssb_settings_admin_enqueue_scripts' ) );
			add_action( 'wp_ajax_activate_plugin', array( $this, 'ssb_activate_plugin' ) );
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 1.0.0
		 */
		public function ssb_settings_admin_enqueue_scripts() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery' );
		}

		/**
		 * Set settings sections.
		 *
		 * @param array $sections Setting sections array.
		 * @return $this
		 */
		public function set_sections( $sections ) {
			$this->settings_sections = $sections;

			return $this;
		}

		/**
		 * Add a single section.
		 *
		 * @param array $section Section array.
		 * @return $this
		 */
		public function add_section( $section ) {
			$this->settings_sections[] = $section;

			return $this;
		}

		/**
		 * Set settings fields.
		 *
		 * @param array $fields Settings fields array.
		 * @return $this
		 */
		public function set_fields( $fields ) {
			$this->settings_fields = $fields;

			return $this;
		}

		/**
		 * Add a field to a section.
		 *
		 * @param string $section Section ID.
		 * @param array  $field Field array.
		 * @return $this
		 */
		public function add_field( $section, $field ) {
			$defaults = array(
				'name'  => '',
				'label' => '',
				'desc'  => '',
				'type'  => 'text',
			);

			$arg                                 = wp_parse_args( $field, $defaults );
			$this->settings_fields[ $section ][] = $arg;

			return $this;
		}

		/**
		 * Initialize and registers the settings sections and fields to WordPress.
		 *
		 * Usually this should be called at `admin_init` hook.
		 *
		 * This function gets the initiated settings sections and fields. Then
		 * registers them to WordPress and ready for use.
		 *
		 * @version 5.3.3
		 * @since 1.0.0
		 */
		public function admin_init() {

			$advanced_settings = array( 'ssb_advanced' );

			// If NextGen plugin is activated and Simple Social Buttons Pro is activated.
			if ( class_exists( 'C_Photocrati_Installer' ) && class_exists( 'Simple_Social_Buttons_Pro' ) ) {
				$advanced_settings = array_merge( $advanced_settings, array( 'ssb_ngg_gallery' ) );
			}

			// Register settings sections.
			foreach ( $this->settings_sections as $section ) {
				if ( false === get_option( $section['id'] ) ) {
					add_option( $section['id'] );
				}

				if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
					$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
					$callback        = call_user_func( array( $this, 'ssb_get_description' ), $section['desc'] );
				} elseif ( isset( $section['callback'] ) ) {
					$callback = $section['callback'];
				} else {
					$callback = null;
				}
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $section['id'], $advanced_settings ) ) {
					add_settings_section( $section['id'], $section['title'], $callback, 'ssb_advanced' );
				} elseif ( 'ssb_click_to_tweet' !== $section['id'] ) {
					add_settings_section( $section['id'], $section['title'], $callback, 'ssb_networks' );
				} else {
					add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
				}
			}
			// Register settings fields.
			foreach ( $this->settings_fields as $section => $field ) {

				foreach ( $field as $index => $option ) {
					$name     = isset( $option['name'] ) && ! empty( $option['name'] ) ? $option['name'] : '';
					$type     = isset( $option['type'] ) ? $option['type'] : 'text';
					$label    = isset( $option['label'] ) ? $option['label'] : '';
					$callback = isset( $option['callback'] ) ? $option['callback'] : array( $this, 'callback_' . $type );

					$args = array(
						'id'                => $name,
						'class'             => isset( $option['class'] ) ? $option['class'] : $name,
						'label_for'         => "{$section}[{$name}]",
						'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
						'help'              => isset( $option['help'] ) ? $option['help'] : '',
						'name'              => $label,
						'section'           => $section,
						'size'              => isset( $option['size'] ) ? $option['size'] : null,
						'options'           => isset( $option['options'] ) ? $option['options'] : '',
						'std'               => isset( $option['default'] ) ? $option['default'] : '',
						'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
						'type'              => $type,
						'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
						'min'               => isset( $option['min'] ) ? $option['min'] : '',
						'max'               => isset( $option['max'] ) ? $option['max'] : '',
						'step'              => isset( $option['step'] ) ? $option['step'] : '',
						'link'              => isset( $option['link'] ) ? $option['link'] : '',
					);

					// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					if ( in_array( $section, $advanced_settings ) ) {
						add_settings_field( "{$section}[{$name}]", $label, $callback, 'ssb_advanced', $section, $args );
					} elseif ( 'ssb_click_to_tweet' !== $section ) {
						add_settings_field( "{$section}[{$name}]", $label, $callback, 'ssb_networks', $section, $args );
					} else {
						add_settings_field( "{$section}[{$name}]", $label, $callback, $section, $section, $args );
					}
				}
			}

			// Creates our settings in the options table.
			foreach ( $this->settings_sections as $section ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $section['id'], $advanced_settings ) ) {
					register_setting( 'ssb_advanced', $section['id'], array( $this, 'ssb_sanitize_options' ) );
				} elseif ( 'ssb_click_to_tweet' !== $section['id'] ) {
					register_setting( 'ssb_networks', $section['id'], array( $this, 'ssb_sanitize_options' ) );
				} else {
					register_setting( $section['id'], $section['id'], array( $this, 'ssb_sanitize_options' ) );
				}
			}
		}

		/**
		 * Get Section Description
		 *
		 * @param string $desc Description.
		 * @return string Description.
		 *
		 * @since 3.2.0
		 */
		public function ssb_get_description( $desc ) {
			return $desc;
		}


		/**
		 * Get field description for display.
		 *
		 * @param array $args Settings field args.
		 * @return string Field description HTML.
		 */
		public function get_field_description( $args ) {
			if ( ! empty( $args['desc'] ) ) {
				$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
			} else {
				$desc = '';
			}

			return $desc;
		}

		/**
		 * Callback for icon style field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public function callback_icon_style( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			?>

		<!-- <div class="postbox">
		<div class="inside"> -->
			<?php foreach ( $args['options'] as $key => $label ) : ?>
			<div class="simplesocialbuttons-style-outer">
				<div class="simplesocialbuttons-style">
				<label>
					<!-- <input type="radio" name="simplesocialbuttons" value="test" <?php echo checked( $value, $key, false ); ?>> -->
					<?php
					printf(
						'<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />',
						esc_attr( $args['section'] ),
						esc_attr( $args['id'] ),
						esc_attr( $key ),
						checked( $value, $key, false )
					);
					?>
					<span class="radio"><span class="shadow"></span></span>
				</label>
				<div class="simplesocialbuttons-nav <?php echo esc_attr( $key ); ?>">
					<?php if ( 'simple-icons' === $key ) : ?>
					<ul>
					<li><a href="#" class="simplesocial-fb-share"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="_1pbq" color="#ffffff">
						<path fill="#ffffff" fill-rule="evenodd" class="icon" d="M8 14H3.667C2.733 13.9 2 13.167 2 12.233V3.667A1.65 1.65 0 0 1 3.667 2h8.666A1.65 1.65 0 0 1
						14 3.667v8.566c0 .934-.733 1.667-1.667 1.767H10v-3.967h1.3l.7-2.066h-2V6.933c0-.466.167-.9.867-.9H12v-1.8c.033 0-.933-.266-1.533-.266-1.267
						0-2.434.7-2.467 2.133v1.867H6v2.066h2V14z"></path></svg></span><span class="simplesocial-hidden-text">Share</span></a></li>
					<li><a href="#" class="simplesocial-twt-share"><span class="icon"><svg viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4.9 0H0L5.782 7.7098L0.315 14H2.17L6.6416 8.8557L10.5 14H15.4L9.3744 5.9654L14.56 0H12.705L8.5148 4.8202L4.9 0ZM11.2 12.6L2.8 
						1.4H4.2L12.6 12.6H11.2Z" fill="#fff"/></svg></span><span class="simplesocial-hidden-text">Post</span></a></li>
					<li><a href="#" class="simplesocial-linkedin-share"><span class="icon"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
					xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="15px" height="14.1px" viewBox="-301.4 387.5 15 14.1"
					enable-background="new -301.4 387.5 15 14.1" xml:space="preserve"> <g id="XMLID_398_"> <path id="XMLID_399_" fill="#FFFFFF"
					d="M-296.2,401.6c0-3.2,0-6.3,0-9.5h0.1c1,0,2,0,2.9,0c0.1,0,0.1,0,0.1,0.1c0,0.4,0,0.8,0,1.2 c0.1-0.1,0.2-0.3,0.3-0.4c0.5-0.7,1.2-1,2.1-1.1c0.8-0.1,1.5,0,
					2.2,0.3c0.7,0.4,1.2,0.8,1.5,1.4c0.4,0.8,0.6,1.7,0.6,2.5 c0,1.8,0,3.6,0,5.4v0.1c-1.1,0-2.1,0-3.2,0c0-0.1,0-0.1,0-0.2c0-1.6,0-3.2,0-4.8c0-0.4,
					0-0.8-0.2-1.2c-0.2-0.7-0.8-1-1.6-1 c-0.8,0.1-1.3,0.5-1.6,1.2c-0.1,0.2-0.1,0.5-0.1,0.8c0,1.7,0,3.4,0,5.1c0,0.2,0,0.2-0.2,0.2c-1,0-1.9,0-2.9,0 C-296.1,
					401.6-296.2,401.6-296.2,401.6z"></path> <path id="XMLID_400_" fill="#FFFFFF" d="M-298,401.6L-298,401.6c-1.1,0-2.1,0-3,0c-0.1,0-0.1,0-0.1-0.1c0-3.1,0-6.1,
					0-9.2 c0-0.1,0-0.1,0.1-0.1c1,0,2,0,2.9,0h0.1C-298,395.3-298,398.5-298,401.6z"></path> <path id="XMLID_401_" fill="#FFFFFF"
					d="M-299.6,390.9c-0.7-0.1-1.2-0.3-1.6-0.8c-0.5-0.8-0.2-2.1,1-2.4c0.6-0.2,1.2-0.1,1.8,0.2 c0.5,0.4,0.7,0.9,0.6,1.5c-0.1,0.7-0.5,1.1-1.1,1.3C-299.1,
					390.8-299.4,390.8-299.6,390.9L-299.6,390.9z"></path> </g> </svg></span><span class="simplesocial-hidden-text">Share</span></a></li>
					<li><span style="line-height: 20px; vertical-align: top; font-weight: bold;display: inline-block;">Official Buttons</span></li>
					</ul>
					<?php else : ?>
					<ul>
					<li><a href="#" class="simplesocial-fb-share"><span class="simplesocial-hidden-text">Facebook</span></a></li>
					<li><a href="#" class="simplesocial-twt-share"><span class="simplesocial-hidden-text">Twitter/X</span></a></li>
					<li><a href="#" class="simplesocial-linkedin-share"><span class="simplesocial-hidden-text">LinkedIn</span></a></li>
					</ul>
				<?php endif; ?>
				</div> <!--  .simplesocialbuttons-nav -->
				</div> <!--  .simplesocialbuttons-style -->
			</div> <!--  .simplesocialbuttons-style-outer -->
			<?php endforeach; ?>
		<!-- </div>
		</div> -->

			<?php
		}

		/**
		 * Callback for position field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public function callback_position( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			?>
			<div class="simplesocial-position-outer-wrapper">
			<?php
			printf(
				'<input type="hidden" name="%1$s[%2$s]" value="" />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			?>
			<?php foreach ( $args['options'] as $key => $label ) : ?>
				<?php $checked = isset( $value[ $key ] ) ? $value[ $key ] : '0'; ?>
				<div class="simplesocial-position-outer">
				<label class="simplesocial-position-box simplesocial-<?php echo esc_attr( $key ); ?>">
					<span class="simplesocial-fb-sharehd-line"><?php echo esc_html( $label ); ?></span>
					<span class="simplesocial-blue-box">
					<span class="simplesocial-highlight">
					</span>
					</span>
					<?php
					printf(
						'<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />',
						esc_attr( $args['section'] ),
						esc_attr( $args['id'] ),
						esc_attr( $key ),
						checked( $checked, $key, false )
					);
					?>
					<span class="checkbox"><span class="shadow"></span></span>
				</label>
				</div>
			<?php endforeach; ?>
			</div>
			<?php
		}

		/**
		 * Callback for SSB select field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public function callback_ssb_select( $args ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo isset( $args['desc'] ) ? $args['desc'] : '';
			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			?>
		<div class="simplesocial-form-section">
		<h5><?php echo esc_html( $args['name'] ); ?></h5>
			<?php
			printf(
				'<select class="%1$s ssb_select" name="%2$s[%3$s]" id="%2$s[%3$s]">',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			foreach ( $args['options'] as $key => $label ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $key ),
					selected( $value, $key, false ),
					esc_html( $label )
				);
			}
			printf( '</select>' );
			?>
		</div>
			<?php
		}

		/**
		 * Callback for SSB checkbox field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public function callback_ssb_checkbox( $args ) {

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo isset( $args['desc'] ) ? $args['desc'] : '';
			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			?>
		<div class="simplesocial-form-section">
		<h5><?php echo esc_html( $args['name'] ); ?></h5>
			<?php
			printf(
				'<input type="hidden" name="%1$s[%2$s]" value="0" />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			printf(
				'<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="1" %3$s />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				checked( $value, '1', false )
			);
			printf(
				'<label class="simplesocial-switch" for="%1$s[%2$s]"></label>',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo isset( $args['help'] ) ? $args['help'] : '';
			?>
			</div>
			<?php
		}

		/**
		 * Callback for SSB color field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public function callback_ssb_color( $args ) {

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo isset( $args['desc'] ) ? $args['desc'] : '';
			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			?>
		<div class="simplesocial-form-section">
		<h5><?php echo esc_html( $args['name'] ); ?></h5>
		<div class="selection-color">
			<?php
			printf(
				'<input type="text" class="%1$s-text ssb_settings_color_picker" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value ),
				esc_attr( $args['std'] )
			);
			?>
		</div>
		</div>
			<?php
		}

		/**
		 * Callback for SSB post types field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public function callback_ssb_post_types( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$html  = '<fieldset>';
			?>
		<h4><?php esc_html_e( 'Post Type Settings', 'simple-social-buttons' ); ?></h4>
		<div class="simplesocial-inline-form-section">
			<?php
			printf(
				'<input type="hidden" name="%1$s[%2$s]" value="" />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			?>
			<?php
			foreach ( $args['options'] as $key => $label ) :

				$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
				printf(
					'<label for="%1$s[%2$s][%3$s]">',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $key )
				);
				printf(
					'<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $key ),
					checked( $checked, $key, false )
				);
				printf( '<span class="checkbox"><span class="shadow"></span></span>' );
				printf( '%1$s</label>', esc_html( $label ) );

			endforeach;
			?>

		</div> <!--  .form-section -->

			<?php
		}

		/**
		 * Create a callback for text.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 * @version 4.0.0
		 */
		public function callback_ssb_text( $args ) {

			$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo isset( $args['desc'] ) ? $args['desc'] : '';
			?>
			<div class="<?php printf( 'simplesocial-form-section container-%1$s[%2$s]', esc_attr( $args['section'] ), esc_attr( $args['id'] ) ); ?>">
				<h5><?php echo esc_html( $args['name'] ); ?></h5>
				<div class="simplesocial-input">
					<?php
					printf(
						'<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>',
						esc_attr( $type ),
						esc_attr( $size ),
						esc_attr( $args['section'] ),
						esc_attr( $args['id'] ),
						esc_attr( $value ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$placeholder
					);
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Create a callback for textarea.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 * @version 5.0.0
		 */
		public function callback_ssb_textarea( $args ) {

			$value       = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
			$help        = isset( $args['help'] ) ? $args['help'] : '';

			?>
			<div class="simplesocial-form-section">
				<h5><?php echo esc_html( $args['name'] ); ?></h5>
				<div class="simplesocial-input">
					<?php
					if ( 'ssb_js' === $args['id'] ) {
						printf(
							'<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>',
							esc_attr( $size ),
							esc_attr( $args['section'] ),
							esc_attr( $args['id'] ),
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$placeholder,
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$this->ssb_escape_js_output( $value )
						);
					} else {
						printf(
							'<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>',
							esc_attr( $size ),
							esc_attr( $args['section'] ),
							esc_attr( $args['id'] ),
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$placeholder,
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$this->ssb_escape_output( $value )
						);
					}
					echo wp_kses_post( $help );
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * The JS Output Escaping.
		 *
		 * @param string $input Input string.
		 * @return string|false Decoded string or false on failure.
		 * @since 4.0.0
		 */
		public function ssb_escape_js_output( $input ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			return base64_decode( $input );
		}

		/**
		 * The CSS Output Escaping.
		 *
		 * @param string $input Input string.
		 * @return string Input string.
		 * @since 4.0.0
		 */
		public function ssb_escape_output( $input ) {
			return $input;
		}

		/**
		 * Callback for SSB icon selection field.
		 *
		 * @param array $args Field arguments.
		 * @return void
		 * @since 1.0.0
		 * @version 7.0.1
		 */
		public function callback_ssb_icon_selection( $args ) {

			$save_value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$settings   = array_flip( array_merge( array( 0 ), explode( ',', $save_value ) ) );

			?>

		<div class="inside">
			<p>Drag & Drop to activate and order your share buttons:</p>
			<div class="ssb_settings_box">
			<h2>Active</h2>
			<ul id="ssb_active_icons" class="items" style="min-height:35px">
				<?php
				$ssb_icons_order   = array();
				$arr_known_buttons = ssb_get_known_buttons();
				foreach ( $arr_known_buttons as $button_name ) {
					$ssb_icons_order[ $button_name ] = isset( $settings[ $button_name ] ) ? $settings[ $button_name ] : 0;
				}

				asort( $ssb_icons_order );
				?>
				<?php foreach ( $ssb_icons_order as $key => $value ) : ?>
						<?php if ( 0 !== (int) $value ) : ?>
					<li data-id="<?php echo esc_attr( $key ); ?>" class="list">
					<img src="<?php echo esc_url( plugins_url( 'assets/images/' . $key . '.svg', plugin_dir_path( __FILE__ ) ) ); ?>" /></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			</div>
			<div class="ssb_settings_box">
			<h2>InActive</h2>

			<ul id="ssb_inactive_icons" class="items" style="min-height:35px">
				<?php foreach ( $ssb_icons_order as $key => $value ) : ?>
						<?php if ( 0 === (int) $value ) : ?>
				<li data-id="<?php echo esc_attr( $key ); ?>" class="list" >
				<img src="<?php echo esc_url( plugins_url( 'assets/images/' . $key . '.svg', plugin_dir_path( __FILE__ ) ) ); ?>" /></li>
				<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>

				<?php
				printf(
					'<input type="hidden" id="%1$s[%2$s]" name="%1$s[%2$s]" value="%3$s" />',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $save_value )
				);
				?>

		</div>

			<?php
		}

		/**
		 * Create a callback for upgrade/activate pro Button.
		 *
		 * @param array $args An array of arguments for the settings structure.
		 *
		 * @version 6.1.0
		 */
		public function callback_ssb_go_pro( $args ) {
			// Check if the Pro version is installed.
			$pro_plugin_path  = 'simple-social-buttons-pro/simple-social-buttons-pro.php';
			$is_pro_installed = file_exists( WP_PLUGIN_DIR . '/' . $pro_plugin_path );
			$is_pro_active    = is_plugin_active( $pro_plugin_path );

			if ( $is_pro_installed && ! $is_pro_active ) {
				// Pro version is installed but not activated.
				$button_text   = 'Click here to Activate Pro';
				$button_action = 'activate';
			} else {
				// Pro version is not installed.
				$button_text   = 'Click here to Upgrade';
				$button_action = 'upgrade';
			}
			?>
			<div class="ssb_goto_pro_section">
				<h4><?php echo esc_html( $args['name'] ); ?></h4>
				<p><?php echo esc_html( $args['desc'] ); ?></p>
				<a href="<?php echo esc_url( $args['link'] ); ?>" class="ssb_goto_pro_button" 
				data-action="<?php echo esc_attr( $button_action ); ?>" data-plugin="<?php echo esc_attr( $pro_plugin_path ); ?>"><?php echo esc_html( $button_text ); ?></a>
			</div>
			<script>
			jQuery(document).ready(function($) {
				$('.ssb_goto_pro_button').on('click', function(e) {
					e.preventDefault();
					var action = $(this).data('action');
					var plugin = $(this).data('plugin');
					if (action === 'activate') {
						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'activate_plugin',
								plugin: plugin,
								_wpnonce: '<?php echo esc_attr( wp_create_nonce( 'activate-plugin_' . $pro_plugin_path ) ); ?>'
							},
							success: function(response) {
								if (response.success) {
									location.reload();
								} else {
									alert('Failed to activate the plugin.');
								}
							}
						});
					} else {
						window.location.href = $(this).attr('href');
					}
				});
			});
			</script>
			<?php
		}

		/**
		 * Page top banner content.
		 *
		 * @since 7.0.0
		 * @return void
		 */
		public static function ssb_banner_content() {
			?>
			<div id="ssb-page-top-banner" class="ssb-top-notification-bar">
				<div class="ssb-notification-container">
					
				<div class="ssb-notifications-logo">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-social-buttons' ) ); ?>">
					<img src="<?php echo esc_url( plugins_url( 'assets/images/ssb-logo.svg', plugin_dir_path( __FILE__ ) ) ); ?>" alt="Simple Social Buttons"></a>

				</div>
				<div class="ssb-notifications-actions">

	<a
		href="<?php echo esc_url( class_exists( 'Simple_Social_Buttons_Pro' ) ? 'https://simplesocialbuttons.com/contact/' : 'https://simplesocialbuttons.com/pricing/' ); ?>"
		target="_blank"
		class="ssb-update-button"
	>
		<svg
			width="20"
			height="20"
			viewBox="0 0 20 20"
			fill="currentColor"
			xmlns="http://www.w3.org/2000/svg"
		>
			<path
				d="M19.947 7.18097C19.8842 6.99583 19.7685 6.83316 19.6142 6.71303
				C19.46 6.5929 19.2739 6.52057 19.079 6.50497L13.378 6.05197
				L10.911 0.590968C10.8325 0.41508 10.7047 0.265689 10.5431 0.160825
				C10.3815 0.0559612 10.193 0.000105879 10.0004 1.50379e-07
				C9.80771 -0.000105578 9.61916 0.0555428 9.45745 0.160229
				C9.29574 0.264916 9.16779 0.414166 9.08903 0.589968L6.62203 6.05197
				L0.921026 6.50497C0.729482 6.52014 0.546364 6.59018 0.393581 6.7067
				C0.240798 6.82322 0.124819 6.98129 0.0595194 7.162
				C-0.00578038 7.34271 -0.0176359 7.5384 0.0253712 7.72567
				C0.0683784 7.91294 0.164427 8.08385 0.302026 8.21797
				L4.51503 12.325L3.02503 18.777C2.97978 18.9723 2.99428 19.1767
				3.06665 19.3636C3.13901 19.5506 3.26589 19.7115 3.43083 19.8254
				C3.59577 19.9394 3.79115 20.0011 3.99161 20.0026
				C4.19208 20.0042 4.38837 19.9454 4.55503 19.834L10 16.204
				L15.445 19.834C15.6154 19.9471 15.8162 20.0053 16.0207 20.0008
				C16.2251 19.9963 16.4232 19.9293 16.5884 19.8089
				C16.7536 19.6884 16.878 19.5203 16.9448 19.327
				C17.0116 19.1338 17.0176 18.9247 16.962 18.728L15.133 12.328
				L19.669 8.24597C19.966 7.97797 20.075 7.55997 19.947 7.18097Z"
				fill="currentColor"
			/>
		</svg>
			<?php
			if ( class_exists( 'Simple_Social_Buttons_Pro' ) ) {
				esc_html_e( 'Support', 'simple-social-buttons' );
			} else {
				esc_html_e( 'Upgrade to Pro', 'simple-social-buttons' );
			}
			?>
	</a>

	<a
		href="https://simplesocialbuttons.com/documentation/"
		target="_blank"
		class="ssb-doc-button"
	>
		<svg
			width="16"
			height="20"
			viewBox="0 0 16 20"
			fill="currentColor"
			xmlns="http://www.w3.org/2000/svg"
		>
			<path
				fill-rule="evenodd"
				clip-rule="evenodd"
				d="M16 19.1719H0V0H12.1166L16 4.12365V19.1719ZM13.8781
				15.7223H2.43862V14.8517H13.8797V15.7223H13.8781ZM10.054
				4.93496H2.43862V4.06904H10.0556V4.93496H10.054ZM13.6254
				7.38761H2.43862V6.51702H13.6254V7.38761ZM7.37981
				10.2054H2.43862V9.33944H7.37981V10.2054ZM13.8781
				12.8999H2.43862V12.0339H13.8797V12.8999H13.8781Z"
				fill="currentColor"
			/>
		</svg>
			<?php esc_html_e( 'Documentation', 'simple-social-buttons' ); ?>
	</a>

</div>
				</div>
			</div>
			<?php
		}

		/**
		 * AJAX callback for activating a SSB Pro.
		 *
		 * @since 6.1.0
		 *
		 * @return void
		 */
		public function ssb_activate_plugin() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				wp_send_json_error( 'You do not have permission to activate plugins.' );
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
			check_ajax_referer( 'activate-plugin_' . $plugin );

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$plugin_path = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
			$result      = activate_plugin( $plugin_path );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result->get_error_message() );
			}

			wp_send_json_success();
		}

		/**
		 * Settings header.
		 *
		 * @return void
		 */
		public function settings_header() {
			?>
		<div class="ssb-top-bar">
		<a href="https://wpbrigade.com/"><img src="<?php echo esc_url( plugins_url( 'assets/images/social_button.svg', plugin_dir_path( __FILE__ ) ) ); ?>" 
		alt="Simple Social Buttons"></a>
		<div class="ssb-top-bar-content">
			<h2>Simple Social Buttons -->> <?php esc_html_e( 'makes Social Sharing easy for everyone', 'simple-social-buttons' ); ?></h2>
			<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %1$s and %5$s are the opening strong tag, %2$s and %4$s are the closing strong tag.
					__(
						'%1$sSimple Social Buttons%2$s by %1$s%3$sWPBrigade%4$s%5$s adds an advanced set of social media sharing buttons to your WordPress sites, such as:
						%1$sFacebook%2$s, %1$sTwitter%2$s, %1$sLinkedIn%2$s, %1$sWhatsApp%2$s, %1$sViber%2$s, %1$sReddit%2$s and %1$sPinterest%2$s.
						This makes it the most flexible social sharing plugin ever for Everyone.',
						'simple-social-buttons'
					),
					'<strong>',
					'</strong>',
					'<a href="https://wpbrigade.com/?utm_source=simple-social-buttons-lite&utm_medium=link-header&utm_campaign=pro-upgrade">',
					'</a>',
					'</strong>'
				)
			);
			?>
			</p>
		</div>
		</div>
			<?php
		}

		/**
		 * Settings sidebar.
		 *
		 * @return void
		 */
		public function settings_sidebar() {
			?>
	<div class="postbox-container ssb_right_sidebar">
		<div id="poststuff">
		<div class="postbox ssb_social_links_wrapper ssb_spread_word">
			<div class="sidebar postbox">
			<h2><?php esc_html_e( 'Spread the Word', 'simple-social-buttons' ); ?></h2>
			<ul class="ssb_social_links">
				<li>
				<a href="https://twitter.com/intent/tweet?text=Check out this (FREE) Amazing Social Share Plugin for WordPress&amp;
				url=https://wordpress.org/plugins/simple-social-buttons/" data-count="none" class="button twitter" target="_blank"
				title="Post to Twitter Now"><?php esc_html_e( 'Share on X/Twitter', 'simple-social-buttons' ); ?><span class="dashicons ssb-x-icon"></span></a>
				</li>
				<li>
				<a href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org/plugins/simple-social-buttons/" class="button facebook" target="_blank" 
				title="Check out this (FREE) Amazing Social Share Plugin for WordPress"><?php esc_html_e( 'Share on Facebook', 'simple-social-buttons' ); ?>
				<span class="dashicons dashicons-facebook-alt"></span>
				</a>
				</li>
				<li>
				<a href="https://wordpress.org/support/plugin/simple-social-buttons/reviews/?filter=5" class="button wordpress" target="_blank" title="Rate on WordPress.org">
				<?php esc_html_e( 'Rate on WordPress.org', 'simple-social-buttons' ); ?><span class="dashicons dashicons-wordpress"></span>
				</a>
				</li>
			</ul>
			</div>
		</div>

		<div class="postbox ssb_social_links_wrapper ssb_newsletter">
			<div class="sidebar postbox">
			<h2><?php esc_html_e( 'Subscribe Newsletter', 'simple-social-buttons' ); ?></h2>
			<ul class="postbox-newsletter">
				<li>
				<label for=""><?php esc_html_e( 'Email', 'simple-social-buttons' ); ?></label>
				<input type="email" name="subscriber_mail" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" id="ssb_subscribe_mail">
				<p class="ssb_subscribe_warning"></p>
				</li>
				<li>
				<label for=""><?php esc_html_e( 'Name', 'simple-social-buttons' ); ?></label>
				<input type="text" name="subscriber_name" id="ssb_subscribe_name" value="<?php echo esc_attr( wp_get_current_user()->display_name ); ?>">
				</li>
				<li>
				<input type="submit" value="Subscribe Now" class="button button-primary button-big" id="ssb_subscribe_btn">
				<img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" class="ssb_subscribe_loader" style="display:none">
				</li>
				<li>
				<p class="ssb_return_message"></p>
				</li>
			</ul>
			</div>
		</div>

		<div class="postbox ssb_social_links_wrapper ssb_other_plugin">
			<div class="sidebar postbox">
			<h2><?php esc_html_e( 'Recommended Plugins', 'simple-social-buttons' ); ?></h2>
			<ul class="plugins_lists">
				<li>
				<a href="https://loginpress.pro/?utm_source=ssb-lite&amp;utm_medium=sidebar&amp;utm_campaign=pro-upgrade&utm_content=text-link" target="_blank" 
				title="Post to Twitter Now"><?php esc_html_e( 'Customize WordPress Login Page', 'simple-social-buttons' ); ?></a>
				</li>
				<li>
				<a href="https://analytify.io/ref/73/?utm_source=ssb-lite&amp;utm_medium=sidebar&amp;utm_campaign=pro-upgrade&utm_content=text-link" target="_blank" 
				title="Share with your facebook friends about this awesome plugin."><?php esc_html_e( 'Simplify Google Analytics in WordPress', 'simple-social-buttons' ); ?></a>
				</li>
				<li>
				<a href="https://wpbrigade.com/wordpress/plugins/related-posts/?utm_source=ssb-lite&amp;utm_medium=sidebar&amp;utm_campaign=pro-upgrade&utm_content=text-link"
				target="_blank" title="Related Posts Thumbnails"><?php esc_html_e( 'Related Posts Thumbnails', 'simple-social-buttons' ); ?></a>
				</li>
				<li>
				<a href="https://wpbrigade.com/recommend/maintenance-mode&utm_content=text-link" target="_blank" 
				title="Under Construction &amp; Maintenance mode"><?php esc_html_e( 'Under Construction & Maintenance mode', 'simple-social-buttons' ); ?></a>
				</a>
				</li>
			</ul>
			</div>
		</div>
		</div>
	</div>
			<?php
		}

		/**
		 * Displays a text field for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_text( $args ) {

			$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';

			$html = sprintf(
				'<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>',
				esc_attr( $type ),
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$placeholder
			);
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a url field for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_url( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a number field for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_number( $args ) {
			$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'number';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
			$min         = empty( $args['min'] ) ? '' : ' min="' . esc_attr( $args['min'] ) . '"';
			$max         = empty( $args['max'] ) ? '' : ' max="' . esc_attr( $args['max'] ) . '"';
			$step        = empty( $args['step'] ) ? '' : ' step="' . esc_attr( $args['step'] ) . '"';

			$html = sprintf(
				'<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>',
				esc_attr( $type ),
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$placeholder,
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$min,
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$max,
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$step
			);
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a checkbox for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_checkbox( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html  = '<fieldset>';
			$html .= sprintf(
				'<label for="%1$s[%2$s]">',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			$html .= sprintf(
				'<input type="hidden" name="%1$s[%2$s]" value="off" />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			$html .= sprintf(
				'<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				checked( $value, 'on', false )
			);
			$html .= sprintf( '%1$s</label>', wp_kses_post( $args['desc'] ) );
			$html .= '</fieldset>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a multicheckbox for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_multicheck( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$html  = '<fieldset>';
			$html .= sprintf(
				'<input type="hidden" name="%1$s[%2$s]" value="" />',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);
			foreach ( $args['options'] as $key => $label ) {
				$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
				$html   .= sprintf(
					'<label for="%1$s[%2$s][%3$s]">',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $key )
				);
				$html   .= sprintf(
					'<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $key ),
					checked( $checked, $key, false )
				);
				$html   .= sprintf( '%1$s</label><br>', esc_html( $label ) );
			}

			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a radio button for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_radio( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$html  = '<fieldset>';

			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf(
					'<label for="%1$s[%2$s][%3$s]">',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $key )
				);
				$html .= sprintf(
					'<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $key ),
					checked( $value, $key, false )
				);
				$html .= sprintf( '%1$s</label><br>', esc_html( $label ) );
			}

			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a selectbox for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_select( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$html  = sprintf(
				'<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] )
			);

			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $key ),
					selected( $value, $key, false ),
					esc_html( $label )
				);
			}

			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a textarea for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_textarea( $args ) {

			$value       = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';

			$html = sprintf(
				'<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$placeholder,
				esc_textarea( $value )
			);
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays the html for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_html( $args ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_field_description( $args );
		}

		/**
		 * Displays a rich text textarea for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_wysiwyg( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

			echo '<div style="max-width: ' . esc_attr( $size ) . ';">';

			$editor_settings = array(
				'teeny'         => true,
				'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
				'textarea_rows' => 10,
			);

			if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
				$editor_settings = array_merge( $editor_settings, $args['options'] );
			}

			wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

			echo '</div>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_field_description( $args );
		}

		/**
		 * Displays a file upload field for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_file( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$id    = $args['section'] . '[' . $args['id'] . ']';
			$label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File', 'simple-social-buttons' );

			$html  = sprintf(
				'<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value )
			);
			$html .= '<input type="button" class="button wpsa-browse" value="' . esc_attr( $label ) . '" />';
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a password field for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_password( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf(
				'<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value )
			);
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Displays a color picker field for a settings field.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_color( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf(
				'<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />',
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value ),
				esc_attr( $args['std'] )
			);
			$html .= $this->get_field_description( $args );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}


		/**
		 * Displays a select box for creating the pages select box.
		 *
		 * @param array $args Settings field args.
		 * @return void
		 */
		public function callback_pages( $args ) {

			$dropdown_args = array(
				'selected' => esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) ),
				'name'     => $args['section'] . '[' . $args['id'] . ']',
				'id'       => $args['section'] . '[' . $args['id'] . ']',
				'echo'     => 0,
			);
			$html          = wp_dropdown_pages( $dropdown_args ); // phpcs:ignore
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Sanitize callback for Settings API.
		 *
		 * @param array $options Options array.
		 * @return array Sanitized options.
		 */
		public function ssb_sanitize_options( $options ) {

			if ( ! $options ) {
				return $options;
			}

			foreach ( $options as $option_slug => $option_value ) {
				$sanitize_callback = $this->get_sanitize_callback( $option_slug );

				// If callback is set, call it.
				if ( $sanitize_callback ) {
					$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
					continue;
				}
			}

			return $options;
		}

		/**
		 * Get sanitization callback for given option slug.
		 *
		 * @param string $slug Option slug.
		 * @return mixed|false String or bool false.
		 */
		public function get_sanitize_callback( $slug = '' ) {
			if ( empty( $slug ) ) {
				return false;
			}

			// Iterate over registered fields and see if we can find proper callback.
			foreach ( $this->settings_fields as $section => $options ) {
				foreach ( $options as $option ) {
					if ( $option['name'] !== $slug ) {
							continue;
					}

					// Return the callback name.
					return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
				}
			}

			return false;
		}

		/**
		 * Get the value of a settings field.
		 *
		 * @param string $option Settings field name.
		 * @param string $section The section name this field belongs to.
		 * @param string $default Default text if it's not found.
		 * @return string Field value.
		 */
		public function get_option( $option, $section, $default = '' ) {  // phpcs:ignore

			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		/**
		 * Show navigations as tab.
		 *
		 * Shows all the settings section labels as tab.
		 *
		 * @return void
		 */
		public function show_navigation() {
			$html = '<h2 class="nav-tab-wrapper">';

			$tabs = array(
				array(
					'id'    => 'ssb_settings',
					'title' => '<span class="dashicons dashicons-admin-generic"></span>Settings',
				),
			);

			if ( class_exists( 'Simple_Social_Buttons_Pro' ) ) {
				$tabs[] = array(
					'id'    => 'ssb_click_to_tweet',
					'title' => '<span class="dashicons ssb-x-icon"></span>Click To Post',
				);
			}

			$tabs[] = array(
				'id'    => 'ssb_advanced',
				'title' => '<span class="dashicons dashicons-editor-code"></span>Advanced',
			);

			if ( ! class_exists( 'Simple_Social_Buttons_Pro' ) ) {
				$tabs[] = array(
					'id'    => 'ssb_go_pro',
					'title' => '<span class="dashicons dashicons-star-filled"></span>Upgrade To Pro For More Features',
					'link'  => 'https://simplesocialbuttons.com/pricing/?utm_source=simple-social-buttons-lite&utm_medium=tab&utm_campaign=pro-upgrade',
				);
			}
			foreach ( $tabs as $tab ) {
				if ( isset( $tab['link'] ) ) {
					$html .= sprintf(
						'<a href="%3$s" class="nav-tab" id="%1$s-tab" target="_blank" >%2$s</a>',
						esc_attr( $tab['id'] ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$tab['title'],
						esc_url( $tab['link'] )
					);
				} else {
					$html .= sprintf(
						'<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>',
						esc_attr( $tab['id'] ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$tab['title']
					);
				}
			}

			$html .= '</h2>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Show the section settings forms.
		 *
		 * This function displays every sections in a different form.
		 *
		 * @return void
		 * @version 5.3.3
		 */
		public function show_forms() {
			echo '<div class="ssb_settings_container ssb_settings-tab group" id="ssb_settings-tab-content">';
			echo '<div class="metabox-holder">';
				echo '<div id="poststuff">';
			echo '<form method="post" action="options.php">';
				$this->do_settings_sections( 'ssb_networks' );
				settings_fields( 'ssb_networks' );
				submit_button();
			echo '</form>';
				echo '</div>';
			echo '</div>';
			echo '</div>';

			echo '<div class="ssb_settings_container ssb_click_to_tweet-tab group" id="ssb_click_to_tweet-tab-content">';
			echo '<div class="metabox-holder">';
				echo '<div id="poststuff">';
			echo '<form method="post" action="options.php">';
			$this->do_settings_sections( 'ssb_click_to_tweet' );
			settings_fields( 'ssb_click_to_tweet' );
			$this->render_click_to_tweet();
			submit_button();
			echo '</form>';
				echo '</div>';
			echo '</div>';
			echo '</div>';

			echo '<div class="ssb_settings_container ssb_advanced-tab group" id="ssb_advanced-tab-content">';
			echo '<div class="metabox-holder">';
			echo '<div id="poststuff">';
			echo '<form method="post" action="options.php">';
			/**
			 * SSB Advanced settings section.
			 * SSB NextGen Gallery in Advanced settings section if NextGen plugin is activated.
			 */
			$this->do_settings_sections( 'ssb_advanced' );
			settings_fields( 'ssb_advanced' );

				submit_button();
			echo '</form>';
				echo '</div>';
			echo '</div>';
			echo '</div>';

			$this->script();
		}

		/**
		 * Render the click to post structure.
		 *
		 * @since 1.0.0
		 * @version 4.0.4
		 * @return void
		 */
		public function render_click_to_tweet() {
			$js_code = "javascript:window.open(this.dataset.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
			?>
			<div class="postbox" id="ssb_click_to_tweet_design">

						<div class="ssb_ctt_desgin">
							<div class="ssb-ctt-wrapper  twitter-round" data-theme="twitter-round">
									<a data-href="https://twitter.com/intent/tweet?text=Social media is about the people! Not about your business. Provide for the people
									and the people will provide you.&url=https://simplesocialbuttons.com&via=wpbrigade" rel="nofollow" onclick="<?php echo esc_js( $js_code ); ?>">
											<span class="ssb-ctt">
												<span class="ssb-ctt-text">
												<?php
												echo esc_html__(
													'Social media is about the people! Not about your business. Provide for the people and the people will provide you.',
													'simple-social-buttons'
												);
												?>
												</span>
												<span class="ssb-ctt-btn">
														<?php esc_html_e( 'Click to post', 'simple-social-buttons' ); ?>
														<span id="twitter_icon_ctt" class="ssb-x-icon"></span>
												</span>
											</span>
									</a>
								</div>
						</div>
			</div>
			<?php
		}
		/**
		 * Prints out all settings sections added to a particular settings page.
		 *
		 * Part of the Settings API. Use this in a settings page callback function
		 * to output all the sections and fields that were added to that $page with
		 * add_settings_section() and add_settings_field().
		 *
		 * @global $wp_settings_sections Storage array of all settings sections added to admin pages.
		 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections.
		 * @since 2.7.0
		 *
		 * @param string $page The slug name of the page whose settings sections you want to output.
		 * @return void
		 */
		public function do_settings_sections( $page ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {

				if ( $section['callback'] ) {
					call_user_func( $section['callback'], $section );
				}

				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}

				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				$extra_class = in_array( $section['id'], array( 'ssb_sidebar', 'ssb_inline', 'ssb_media', 'ssb_popup', 'ssb_flyin' ) ) ? 'simpleshare-acordions' : '';
				?>
		<div class="postbox <?php echo esc_attr( $extra_class ); ?>" id='<?php echo esc_attr( $section['id'] ); ?>' >
			<div class="inside">
			<h3 class="simpleshare-active ssb-react-section-title"><?php echo esc_html( $section['title'] ); ?></h3>
			<div class="postbox-content">
				<?php
					$this->do_settings_fields( $page, $section['id'] );
				?>
			</div>
			</div>
		</div>
				<?php
			}
		}


		/**
		 * Print out the settings fields for a particular settings section
		 *
		 * Part of the Settings API. Use this in a settings page to output
		 * a specific section. Should normally be called by do_settings_sections()
		 * rather than directly.
		 *
		 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
		 *
		 * @since 2.7.0
		 *
		 * @param string $page Slug title of the admin page who's settings fields you want to show.
		 * @param string $section Slug title of the settings section who's fields you want to show.
		 * @return void
		 */
		public function do_settings_fields( $page, $section ) {
			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
				$class = '';

				if ( ! empty( $field['args']['class'] ) ) {
					$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
				}

				call_user_func( $field['callback'], $field['args'] );
			}
		}


		/**
		 * Tabbable JavaScript codes & Initiate Color Picker.
		 *
		 * This code uses localstorage for displaying active tabs.
		 *
		 * @return void
		 */
		public function script() {
			?>
		<script>
		jQuery(document).ready(function($) {
		//Initiate Color Picker
		$('.wp-color-picker-field').wpColorPicker();

		// Switches option sections
		$('.group').hide();
		var activetab = '';
		if (typeof(localStorage) != 'undefined' ) {
			activetab = localStorage.getItem("activetab");
		}

		//if url has section id as hash then set it as active or override the current local storage value
		if(window.location.hash){
			activetab = window.location.hash;
			if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("activetab", activetab);
			}
		}

		$(activetab+'-tab-content').fadeIn();
		if (activetab != '' && $(activetab).length ) {
			$(activetab).fadeIn();
		} else {
			$('.group:first').fadeIn();
		}
		$('.group .collapsed').each(function(){
			$(this).find('input:checked').parent().parent().parent().nextAll().each(
			function(){
				if ($(this).hasClass('last')) {
				$(this).removeClass('hidden');
				return false;
				}
				$(this).filter('.hidden').removeClass('hidden');
			});
			});

			if (activetab != '' && $(activetab + '-tab').length ) {
			$(activetab + '-tab').addClass('nav-tab-active');
			}
			else {
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
			}
			$('.nav-tab-wrapper a').click(function(evt) {

			if ('ssb_go_pro-tab' == $(this).attr('id')) { return; }
			$('.nav-tab-wrapper a').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active').blur();
			var clicked_group = $(this).attr('href') + '-tab-content';
			if (typeof(localStorage) != 'undefined' ) {
				if ( $(this).attr('href').indexOf('#') > -1 ) {
				localStorage.setItem("activetab", $(this).attr('href'));
				}
			}
			$('.group').hide();
			$(clicked_group).fadeIn();
			evt.preventDefault();
			});

			$('.wpsa-browse').on('click', function (event) {
			event.preventDefault();

			var self = $(this);

			// Create the media frame.
			var file_frame = wp.media.frames.file_frame = wp.media({
				title: self.data('uploader_title'),
				button: {
				text: self.data('uploader_button_text'),
				},
				multiple: false
			});

			file_frame.on('select', function () {
				attachment = file_frame.state().get('selection').first().toJSON();
				self.prev('.wpsa-url').val(attachment.url).change();
			});

			// Finally, open the modal
			file_frame.open();
			});

			$('#ssb_subscribe_btn').on('click', function(event) {
			event.preventDefault();
			var subscriber_mail = $('#ssb_subscribe_mail').val();
			var name = $('#ssb_subscribe_name').val();
			if (!subscriber_mail) {
				$('.ssb_subscribe_warning').html('Please Enter Email');
				return;
			}
			$.ajax({
				url: 'https://wpbrigade.com/wpb-api/wpbrigade/v1/subsribe-to-mailchimp',
				type: 'POST',
				data: {
				subscriber_mail : subscriber_mail,
				name : name,
				plugin_name : 'ssb'
				},
				beforeSend : function() {
				$('.ssb_subscribe_loader').show();
				$('#ssb_subscribe_btn').attr('disabled', 'disabled');
				}
			})
			.done(function(res) {
				$('.ssb_return_message').html(res);
				$('.ssb_subscribe_loader').hide();
			});
			});
			$('.simplesocialbuttons-style').on('click',function(){
			var el = $(this);
			$(this).addClass('social-active').parent().siblings().find('.simplesocialbuttons-style').removeClass('social-active');
			$(this).find('input[type="radio"]').prop('checked', true);
			});
			$('.simplesocial-position-box').on('click',function(){
			var el = $(this);
			var target = $(this).children('input[type="checkbox"]').val();
			if($(this).children('input[type="checkbox"]').is(':checked')){
				$(this).addClass('social-active');
				$('#ssb_'+target).fadeIn();
			}else{
				$(this).removeClass('social-active');
				$('#ssb_'+target).fadeOut();
			}
			$(this).find('.shadow').addClass('animated');
			setTimeout(function(){ el.find('.shadow').removeClass('animated'); }, 400);
			});
			$('.simplesocial-position-box').each(function(){
			var el = $(this);
			var target = $(this).children('input[type="checkbox"]').val();
			if($(this).children('input[type="checkbox"]').is(':checked')){
				$(this).addClass('social-active');
				$('#ssb_'+target).fadeIn();
			}else{
				$(this).removeClass('social-active');
				$('#ssb_'+target).fadeOut();
			}
			});
			$('.simplesocial-inline-form-section label').on('click',function(){
			var el = $(this);
			$(this).find('.shadow').addClass('animated');
			setTimeout(function(){ el.find('.shadow').removeClass('animated'); }, 400);
			});
			$('.simpleshare-acordions h3').on('click',function(){
			$(this).toggleClass('simpleshare-active');
			$(this).next('.postbox-content').slideToggle();
			});
			$('.ssb_select').each(function () {

				// Cache the number of options
				var $this = $(this),
					numberOfOptions = $(this).children('option').length;

				// Hides the select element
				$this.addClass('s-hidden');

				// Wrap the select element in a div
				$this.wrap('<div class="select"></div>');

				// Insert a styled div to sit over the top of the hidden select element
				$this.after('<div class="styledSelect"></div>');

				// Cache the styled div
				var $styledSelect = $this.next('div.styledSelect');
				var getHTML = $this.children('option[value="'+$this.val()+'"]').text();
				// Show the first select option in the styled div
				$styledSelect.text(getHTML);

				// Insert an unordered list after the styled div and also cache the list
				var $list = $('<ul />', {
					'class': 'options'
				}).insertAfter($styledSelect);

				// Insert a list item into the unordered list for each select option
				for (var i = 0; i < numberOfOptions; i++) {
					$('<li />', {
						text: $this.children('option').eq(i).text(),
						rel: $this.children('option').eq(i).val()
					}).appendTo($list);
				}

				// Cache the list items
				var $listItems = $list.children('li');

				// Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
				$styledSelect.click(function (e) {
					if($(this).hasClass('active')){
						$(this).removeClass('active').next('ul.options').slideUp();
					}else{
						$('div.styledSelect.active').each(function () {
						$(this).removeClass('active').next('ul.options').slideUp();
						});
						$(this).addClass('active').next('ul.options').slideDown();
					}
					e.stopPropagation();
				});

				// Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
				// Updates the select element to have the value of the equivalent option
				$listItems.click(function (e) {
					e.stopPropagation();
					$styledSelect.text($(this).text()).removeClass('active');
				var value = $(this).attr('rel').toString();
					$($this).val(value);
					$($this).trigger('change');
					$list.slideUp();
				});
			});
			$(document).off('click.ssbStyledSelect').on('click.ssbStyledSelect', function () {
				$('div.styledSelect.active')
					.removeClass('active')
					.next('ul.options')
					.slideUp();
			});
		});
		</script>
			<?php
			$this->style_fix();
		}

		/**
		 * Style fix for older WordPress versions.
		 *
		 * @return void
		 */
		private function style_fix() {
			global $wp_version;

			if ( version_compare( $wp_version, '3.8', '<=' ) ) :
				?>
			<style type="text/css">
			/** WordPress 3.8 Fix **/
			.form-table th { padding: 20px 10px; }
			#wpbody-content .metabox-holder { padding-top: 5px; }
			</style>
				<?php
			endif;
		}
	}

	endif;
