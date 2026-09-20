<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Metaboxes
 */
class Kiwi_Social_Share_Metaboxes {

	/**
	 * Kiwi_Social_Share_Metaboxes options.
	 *
	 * @var array
	 */
	public $meta_options = array();

	/**
	 * @var bool
	 */
	public $global = false;

	/**
	 * @var array
	 */
	public $post_types = array();

	/**
	 * Kiwi_Social_Share_Metaboxes constructor.
	 */
	public function __construct() {
		$this->prefix = 'kiwi_';

		$this->check_if_global();
		$this->check_post_types();

		/**
		 * Add a metabox and register settings for the shortcodes.
		 */
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		/**
		 * Save the metabox post datas.
		 */
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Call this function to set the value to $meta_options.
		$this->set_meta_options();
	}

	/**
	 * Define the arrays for the meta boxes.
	 */
	public function set_meta_options() {

		$this->meta_options = array(
			/**
			 * Social media title.
			 */
			'social-media-title' => array(
				'name' => esc_html__( 'Social Media Title', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-title',
				'type' => 'text',
			),

			/**
			 * Social media description.
			 */
			'social-media-description' => array(
				'name' => esc_html__( 'Social Media Description', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-description',
				'type' => 'textarea',
			),

			/**
			 * Social media image.
			 */
			'social-media-image' => array(
				'name' => esc_html__( 'Social Media Image', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-image',
				'type' => 'file',
			),

			/**
			 * Social media custom tweet.
			 */
			'social-media-custom-tweet' => array(
				'name' => esc_html__( 'Custom Tweet', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-custom-tweet',
				'type' => 'textarea',
			),

			/**
			 * Social media pinterest image.
			 */
			'social-media-pinterest-image' => array(
				'name' => esc_html__( 'Social Media Pinterest Image', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-pinterest-image',
				'type' => 'file',
			),

			/**
			 * Social media pinterest description.
			 */
			'social-media-pinterest-description' => array(
				'name' => esc_html__( 'Pinterest Description', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-pinterest-description',
				'type' => 'textarea',
			),
		);

	}

	/**
	 * Adds the required metaboxes.
	 */
	public function add_meta_boxes() {

		// Add meta box for shortcode options.
		add_meta_box(
			$this->prefix . 'metabox',
			esc_html__( 'Kiwi Social Share Meta Information', 'kiwi-social-share' ),
			array( $this, 'kiwi_social_share_meta_information' ),
			$this->post_types,
			'advanced'
		);

	}

	/**
	 * Add meta box for Kiwi Social Share meta information options.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function kiwi_social_share_meta_information( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'kiwi_social_share_metabox', 'kiwi_social_share_metabox_nonce' );

		$output       = '';
		$meta_options = $this->meta_options;
		foreach ( $meta_options as $meta_option ) {
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
							<input type="text" class="regular-text" id="<?php echo esc_attr( $meta_option['id'] ); ?>" name="<?php echo esc_attr( $meta_option['id'] ); ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
						</div>
						<?php
						break;

					case 'textarea':
						// Display in the form options.
						?>
						<div class="kss-label">
							<label for="<?php echo esc_attr( $meta_option['id'] ); ?>">
								<?php echo esc_html( $meta_option['name'] ); ?>
							</label>
						</div>

						<div class="kss-options">
							<textarea id="<?php echo esc_attr( $meta_option['id'] ); ?>" name="<?php echo esc_attr( $meta_option['id'] ); ?>" cols="60" rows="10"><?php echo esc_textarea( $meta_value ); ?></textarea>
						</div>
						<?php
						break;

					case 'file':
						// Display in the form options.
						$meta_value_id = get_post_meta( $post->ID, $meta_option['id'] . '_id', true );
						?>
						<div class="kss-label">
							<label for="<?php echo esc_attr( $meta_option['id'] ); ?>">
								<?php echo esc_html( $meta_option['name'] ); ?>
							</label>
						</div>

						<div class="kss-options">
							<input type="text" class="kss-upload-file regular-text" id="<?php echo esc_attr( $meta_option['id'] ); ?>" name="<?php echo esc_attr( $meta_option['id'] ); ?>" value="<?php echo esc_attr( $meta_value ); ?>" />

							<input type="button" class="kss-upload-button button-secondary" value="<?php esc_attr_e( 'Add or Upload File', 'kiwi-social-share' ); ?>" />

							<input type="hidden" class="kss-upload-file-id" id="<?php echo esc_attr( $meta_option['id'] . '_id' ); ?>" name="<?php echo esc_attr( $meta_option['id'] . '_id' ); ?>" value="<?php echo esc_attr( $meta_value_id ); ?>" />

							<div id="<?php echo esc_attr( $meta_option['id'] ); ?>-kss-media-status" class="kss-media-status">
								<?php if ( $meta_value ) { ?>
									<div class="kss-media-item">
										<img style="max-width: 350px; width: 100%;" src="<?php echo esc_url( $meta_value ); ?>" class="kss-file-field-image" alt="" />

										<p class="kss-remove-wrapper">
											<a href="#" class="kss-remove-file-button" rel="<?php echo esc_attr( $meta_option['id'] ); ?>"><?php esc_html_e( 'Remove Image', 'kiwi-social-share' ); ?></a>
										</p>
									</div>
								<?php } ?>
							</div>
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
		if ( empty( $_POST['kiwi_social_share_metabox_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['kiwi_social_share_metabox_nonce'] ), 'kiwi_social_share_metabox' ) ) {
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

				case 'textarea':
					// Get the required form datas.
					$text_value = isset( $_POST[ $meta_option['id'] ] ) ? wp_kses_post( $_POST[ $meta_option['id'] ] ) : $default;

					// Save the datas in the database.
					if ( $text_value ) {
						update_post_meta( $post_id, $meta_option['id'], wp_kses_post( $text_value ) );
					} else {
						delete_post_meta( $post_id, $meta_option['id'] );
					}
					break;

				case 'file':
					// Get the required form datas.
					$media_value    = isset( $_POST[ $meta_option['id'] ] ) ? esc_url_raw( $_POST[ $meta_option['id'] ] ) : $default;
					$media_value_id = isset( $_POST[ $meta_option['id'] . '_id' ] ) ? absint( $_POST[ $meta_option['id'] . '_id' ] ) : '';

					// Save the datas in the database.
					if ( $media_value ) {
						update_post_meta( $post_id, $meta_option['id'], esc_url_raw( $media_value ) );
					} else {
						delete_post_meta( $post_id, $meta_option['id'] );
					}
					// For image id.
					if ( $media_value_id ) {
						update_post_meta( $post_id, $meta_option['id'] . '_id', absint( $media_value_id ) );
					} else {
						delete_post_meta( $post_id, $meta_option['id'] . '_id' );
					}
					break;

			}
		}

	}

	/**
	 * In case we have an 'all' match, we don`t need to search for specific post types.
	 */
	public function check_if_global() {
		$metaboxes = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes_posttypes', '' );
		if ( 'all' == $metaboxes ) {
			$this->global = true;
		}
	}

	/**
	 * Get all the post types where we need to add the custom metaboxes.
	 */
	public function check_post_types() {
		$all_post_types = Kiwi_Social_Share_Helper::get_custom_post_types();
		foreach ( $all_post_types as $k => $v ) {
			$this->post_types[] = $k;
		}

		if ( ! $this->global ) {
			$this->post_types = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes_posttypes_list', array() );
		}
	}
}
