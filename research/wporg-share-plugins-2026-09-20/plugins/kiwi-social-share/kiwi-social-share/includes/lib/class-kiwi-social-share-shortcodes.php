<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Shortcodes
 */
class Kiwi_Social_Share_Shortcodes {

	/**
	 * Kiwi_Social_Share_Shortcodes options.
	 *
	 * @var array
	 */
	public $meta_options = array();

	/**
	 * Kiwi_Social_Share_Shortcodes constructor.
	 *
	 * @param bool $advanced
	 */
	public function __construct( $advanced = false ) {
		if ( $advanced ) {
			/**
			 * Add a custom post type for our shortcodes
			 */
			add_action( 'init', array( $this, 'add_custom_post_type' ) );

			/**
			 * Add a metabox and register settings for the shortcodes.
			 */
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

			/**
			 * Save the metabox post datas.
			 */
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

			/**
			 * Edit the columns from archive and add an identifier (easier to copy paste it where the user needs it)
			 */
			add_filter( 'manage_edit-kiwi-shortcodes_columns', array( $this, 'kiwi_shortcode_columns' ) );
			add_action( 'manage_kiwi-shortcodes_posts_custom_column', array( $this, 'kiwi_shortcode_column' ), 10, 2 );
		}

		// Call this function to set the value to $meta_options.
		$this->set_meta_options();

		add_shortcode( 'kiwi-social-bar', array( $this, 'kiwi_bar_shortcode' ) );
	}

	/**
	 * Define the arrays for the meta boxes.
	 */
	public function set_meta_options() {

		$id      = '';
		$post_id = array();
		if ( ! empty( $_GET ) && ! empty( $_GET['post'] ) ) {
			$id = absint( $_GET['post'] );

			/**
			 * Add shortcode fields.
			 *
			 * This field is used as an identifier (user copies/pastes this content where he needs it).
			 */
			$post_id[] = array(
				'name'       => esc_html__( 'Shortcode list item style', 'kiwi-social-share' ),
				'id'         => 'kiwi_shortcode_identifier',
				'type'       => 'text',
				'default'    => '[kiwi-social-bar id="' . absint( $id ) . '"]',
				'attributes' => array(
					'readonly' => 'readonly',
				),
			);
		}

		$this->meta_options = array(
			/**
			 * Shortcode networks fields ( multicheck ).
			 */
			array(
				'name'    => esc_html__( 'Shortcode networks', 'kiwi-social-share' ),
				'id'      => 'kiwi_shortcode_networks',
				'type'    => 'multicheck',
				'options' => array(
					'facebook'  => esc_html__( 'Facebook', 'kiwi-social-share' ),
					'twitter'   => esc_html__( 'Twitter', 'kiwi-social-share' ),
					'linkedin'  => esc_html__( 'LinkedIn', 'kiwi-social-share' ),
					'pinterest' => esc_html__( 'Pinterest', 'kiwi-social-share' ),
					/* start-pro-version */
					'reddit'    => esc_html__( 'Reddit', 'kiwi-social-share' ),
					'email'     => esc_html__( 'Email', 'kiwi-social-share' ),
					'skype'     => esc_html__( 'Skype', 'kiwi-social-share' ),
					'telegram'  => esc_html__( 'Telegram', 'kiwi-social-share' ),
					'whatsapp'  => esc_html__( 'WhatsApp', 'kiwi-social-share' ),
					/* end-pro-version */
				),
			),

			/**
			 * Shortcode bar style.
			 */
			array(
				'name'    => esc_html__( 'Shortcode bar style', 'kiwi-social-share' ),
				'id'      => 'kiwi_shortcode_bar_style',
				'type'    => 'radio',
				'default' => 'fit',
				'options' => array(
					'fit'    => esc_html__( 'Fit', 'kiwi-social-share' ),
					'center' => esc_html__( 'Center', 'kiwi-social-share' ),
				),
			),

			/**
			 * Shortcode list item styles.
			 */
			array(
				'name'    => esc_html__( 'Shortcode list item style', 'kiwi-social-share' ),
				'id'      => 'kiwi_shortcode_list_item_style',
				'type'    => 'radio',
				'default' => 'rect',
				'options' => array(
					'rect'  => esc_html__( 'Rectangular', 'kiwi-social-share' ),
					'leaf'  => esc_html__( 'Leaf', 'kiwi-social-share' ),
					'shift' => esc_html__( 'Shift', 'kiwi-social-share' ),
					'pill'  => esc_html__( 'Pill', 'kiwi-social-share' ),
				),
			),
		);

		$this->meta_options = array_merge( $post_id, $this->meta_options );

	}

	/**
	 * Adds the required metaboxes.
	 */
	public function add_meta_boxes() {

		// Add meta box for shortcode options.
		add_meta_box(
			'kiwi_shortcode_metabox',
			esc_html__( 'Kiwi Shortcode Meta', 'kiwi-social-share' ),
			array( $this, 'kiwi_shortcode_metabox' ),
			array( 'kiwi-shortcodes' ),
			'advanced'
		);

	}

	/**
	 * Add meta box for shortcode options.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function kiwi_shortcode_metabox( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'kiwi_shortcode_metabox', 'kiwi_shortcode_metabox_nonce' );

		$output       = '';
		$meta_options = $this->meta_options;

		foreach ( $meta_options as $meta_option ) {
			$default    = isset( $meta_option['default'] ) ? $meta_option['default'] : false;
			$meta_value = get_post_meta( $post->ID, $meta_option['id'], true );
			?>
			<div class="kiwi-metabox kss-row">
				<?php
				switch ( $meta_option['type'] ) {

					case 'text':
						// Display in the form options.
						?>
						<div class="kss-label">
							<label for="<?php echo esc_attr( $meta_option['id'] ); ?>">
								<?php echo esc_html( $meta_option['name'] ); ?>
							</label>
						</div>

						<div class="kss-options">
							<?php
							if ( isset( $meta_option['attributes']['readonly'] ) && $meta_option['attributes']['readonly'] ) {
								$text_value = $default;
								$readonly   = 'readonly';
							} else {
								$text_value = $meta_value;
								$readonly   = '';
							}
							?>

							<input type="text" class="regular-text" id="<?php echo esc_attr( $meta_option['id'] ); ?>" name="<?php echo esc_attr( $meta_option['id'] ); ?>" value="<?php echo esc_attr( $text_value ); ?>" <?php echo esc_attr( $readonly ); ?> />
						</div>
						<?php
						break;

					case 'radio':
						// Display in the form options.
						?>
						<div class="kss-label">
							<label for="<?php echo esc_attr( $meta_option['id'] ); ?>">
								<?php echo esc_html( $meta_option['name'] ); ?>
							</label>
						</div>

						<div class="kss-options">
							<ul class="kss-radio-list">
								<?php
								foreach ( (array) $meta_option['options'] as $key => $option ) {
									if ( $key === $default ) {
										$checked = checked( $default, $key, false );
									} else {
										$checked = checked( $meta_value, $key, false );
									}
									?>

									<li>
										<input type="radio" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $meta_option['id'] ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php echo $checked; ?> />

										<label for="<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $option ); ?>
										</label>
									</li>
								<?php } ?>
							</ul>
						</div>
						<?php
						break;

					case 'multicheck':
						// Display in the form options.
						?>
						<div class="kss-label">
							<label for="<?php echo esc_attr( $meta_option['id'] ); ?>">
								<?php echo esc_html( $meta_option['name'] ); ?>
							</label>
						</div>

						<div class="kss-options">
							<ul class="kss-checkbox-list">
								<p class="kss-toggle-wrapper">
									<span class="button-secondary kss-multicheck-toggle">
										<?php esc_html_e( 'Select / Deselect All', 'kiwi-social-share' ); ?>
									</span>
								</p>

								<?php
								foreach ( (array) $meta_option['options'] as $key => $option ) {
									$checked = '';
									foreach ( (array) $meta_value as $social_network ) {
										if ( $key === $social_network ) {
											$checked = checked( $social_network, $key, false );
										}
									}
									?>

									<li>
										<input type="checkbox" class="kss-multicheck" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $meta_option['id'] . '[]' ); ?>" value="<?php echo esc_attr( $key ); ?>"<?php echo $checked; ?> />

										<label for="<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $option ); ?>
										</label>
									</li>
								<?php } ?>

							</ul>
						</div>
						<?php
						break;

				}
				?>
			</div>
			<?php
		}

	}

	/**
	 * Fires once a post has been saved.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_post( $post_id, $post ) {

		$post_id = absint( $post_id );

		// Return if $post_id and $post are not available.
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Return the metabox saves for revisions and autosaves.
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Return if nonce is not set and is not valid.
		if ( empty( $_POST['kiwi_shortcode_metabox_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['kiwi_shortcode_metabox_nonce'] ), 'kiwi_shortcode_metabox' ) ) {
			return;
		}

		// Return if it's being saved in other posts.
		if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
			return;
		}

		// Check if the user has permission to edit or not.
		if ( isset( $_POST['post_type'] ) ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$meta_options = $this->meta_options;
		foreach ( $meta_options as $meta_option ) {
			$default = isset( $meta_option['default'] ) ? $meta_option['default'] : false;

			switch ( $meta_option['type'] ) {

				case 'text':
					// Get the required form datas.
					$text_value = isset( $_POST[ $meta_option['id'] ] ) ? sanitize_text_field( $_POST[ $meta_option['id'] ] ) : $default;

					// Save the datas in the database.
					if ( $text_value ) {
						update_post_meta( $post_id, $meta_option['id'], sanitize_text_field( $text_value ) );
					} else {
						delete_post_meta( $post_id, $meta_option['id'] );
					}
					break;

				case 'radio':
					// Get the required form datas.
					$radio_value = isset( $_POST[ $meta_option['id'] ] ) ? sanitize_key( $_POST[ $meta_option['id'] ] ) : $default;
					$array_keys  = array_keys( $meta_option['options'] );

					// Save the datas in the database.
					if ( in_array( $radio_value, $array_keys, true ) ) {
						update_post_meta( $post_id, $meta_option['id'], sanitize_key( $radio_value ) );
					} else {
						update_post_meta( $post_id, $meta_option['id'], $default );
					}
					break;

				case 'multicheck':
					// Get the required form datas.
					$multicheck_value = isset( $_POST[ $meta_option['id'] ] ) ? wp_unslash( $_POST[ $meta_option['id'] ] ) : array();
					$array_keys       = array_keys( $meta_option['options'] );

					$selected_networks = array();
					foreach ( (array) $multicheck_value as $checked_values ) {
						$selected_networks[] = $checked_values;

						// Save the datas in the database.
						if ( in_array( $checked_values, $array_keys, true ) ) {
							update_post_meta( $post_id, $meta_option['id'], wp_unslash( $selected_networks ) );
						}
					}

					// Delete the datas in the database if array is empty.
					if ( empty( $multicheck_value ) ) {
						update_post_meta( $post_id, $meta_option['id'], array() );
					}
					break;

			}
		}

	}

	/**
	 * Register the custom post type
	 */
	public function add_custom_post_type() {
		register_post_type(
			'kiwi-shortcodes',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'Kiwi Shortcodes', 'kiwi-social-share' ),
					'singular_name'      => esc_html__( 'Kiwi Shortcode', 'kiwi-social-share' ),
					'not_found'          => esc_html__( 'No Kiwi Shortcodes found', 'kiwi-social-share' ),
					'not_found_in_trash' => esc_html__( 'No Kiwi Shortcodes found in trash', 'kiwi-social-share' ),
				),
				'menu_icon'           => 'dashicons-share-alt',
				'supports'            => array( 'title' ),
				'public'              => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'has_archive'         => false,
			)
		);
	}

	/**
	 * Customize the identifier column.
	 *
	 * @param $column
	 * @param $post_id
	 *
	 */
	public function kiwi_shortcode_column( $column, $post_id ) {
		printf( '[kiwi-social-bar id="%s"]', $post_id );
	}

	/**
	 * Customize the kiwi shortcode columns.
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function kiwi_shortcode_columns( $columns ) {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'title'      => esc_html__( 'Shortcode', 'kiwi-social-share' ),
			'identifier' => esc_html__( 'Identifier', 'kiwi-social-share' ),
			'date'       => esc_html__( 'Date', 'kiwi-social-share' ),
		);

		return $columns;
	}

	/**
	 * @param null $atts
	 * @param null $content
	 *
	 * @return mixed|null|string
	 */
	public function kiwi_bar_shortcode( $atts = null, $content = null ) {
		$instance = array(
			'networks' => Kiwi_Social_Share_Helper::get_setting_value( 'networks_article_bar', array() ),
			'style'    => Kiwi_Social_Share_Helper::get_setting_value( 'article_bar_style', 'center' ),
			'items'    => Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' ),
		);

		if ( ! empty( $atts ) ) {
			$instance = array(
				'networks' => get_post_meta( $atts['id'], 'kiwi_shortcode_networks', true ),
				'style'    => get_post_meta( $atts['id'], 'kiwi_shortcode_bar_style', true ),
				'items'    => get_post_meta( $atts['id'], 'kiwi_shortcode_list_item_style', true ),
			);
		}

		$defaults = array(
			'networks' => array(),
			'style'    => 'center',
			'items'    => 'rect',
		);


		$instance = wp_parse_args( $instance, $defaults );
		$bar      = new Kiwi_Social_Share_View_Shortcode_Bar( $instance['networks'], $instance['style'], $instance['items'] );

		return $bar->generate_frontend_bar();
	}

	/**
	 * @param null $atts
	 * @param null $content
	 *
	 * @return mixed|null|string
	 */
	public function kiwi_bar_simple( $atts = null, $content = null ) {
		$defaults = array(
			'networks' => Kiwi_Social_Share_Helper::get_setting_value( 'networks_article_bar', array() ),
			'style'    => Kiwi_Social_Share_Helper::get_setting_value( 'article_bar_style', 'center' ),
			'items'    => Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' )
		);

		$bar = new Kiwi_Social_Share_View_Shortcode_Bar( $defaults['networks'], $defaults['style'], $defaults['items'] );

		return $bar->generate_frontend_bar();
	}
}
