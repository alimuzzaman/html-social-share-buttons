<?php
/**
 * Settings Page
 *
 * Admin settings page for HTML Social Share Buttons
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\Admin;

use HtmlSocialShare\Options\OptionsManager;
use HtmlSocialShare\IconSystem\IconRegistry;

/**
 * Class SettingsPage
 *
 * Manages the admin settings page using WordPress Settings API
 */
class SettingsPage {
	/**
	 * Options manager instance
	 *
	 * @var OptionsManager
	 */
	private $optionsManager;

	/**
	 * Icon registry instance
	 *
	 * @var IconRegistry
	 */
	private $iconRegistry;

	/**
	 * Option name in database
	 *
	 * @var string
	 */
	private $optionName = 'zm_shbt_fld';

	/**
	 * Settings page slug
	 *
	 * @var string
	 */
	private $pageSlug = 'html-social-share-settings';

	/**
	 * Constructor
	 *
	 * @param OptionsManager $optionsManager Options manager instance
	 * @param IconRegistry   $iconRegistry Icon registry instance
	 */
	public function __construct( OptionsManager $optionsManager, IconRegistry $iconRegistry ) {
		$this->optionsManager = $optionsManager;
		$this->iconRegistry   = $iconRegistry;
	}

	/**
	 * Register settings page
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'addMenuPage' ) );
		add_action( 'admin_init', array( $this, 'registerSettings' ) );
	}

	/**
	 * Add settings page to admin menu
	 */
	public function addMenuPage() {
		add_options_page(
			__( 'HTML Social Share Settings', 'html-social-share' ),
			__( 'Social Share Buttons', 'html-social-share' ),
			'manage_options',
			$this->pageSlug,
			array( $this, 'renderPage' )
		);
	}

	/**
	 * Register settings with WordPress Settings API
	 */
	public function registerSettings() {
		register_setting(
			$this->pageSlug,
			$this->optionName,
			array(
				'sanitize_callback' => array( $this, 'sanitizeOptions' ),
			)
		);

		// General Settings Section
		add_settings_section(
			'general_settings',
			__( 'General Settings', 'html-social-share' ),
			array( $this, 'renderGeneralSection' ),
			$this->pageSlug
		);

		// Title field
		add_settings_field(
			'title',
			__( 'Button Title', 'html-social-share' ),
			array( $this, 'renderTitleField' ),
			$this->pageSlug,
			'general_settings'
		);

		// Excludes field
		add_settings_field(
			'excludes',
			__( 'Exclude Posts', 'html-social-share' ),
			array( $this, 'renderExcludesField' ),
			$this->pageSlug,
			'general_settings'
		);

		// Display Settings Section
		add_settings_section(
			'display_settings',
			__( 'Display Settings', 'html-social-share' ),
			array( $this, 'renderDisplaySection' ),
			$this->pageSlug
		);

		// Iconset selector
		add_settings_field(
			'iconset',
			__( 'Icon Style', 'html-social-share' ),
			array( $this, 'renderIconsetField' ),
			$this->pageSlug,
			'display_settings'
		);

		// Placement Settings Section
		add_settings_section(
			'placement_settings',
			__( 'Placement Settings', 'html-social-share' ),
			array( $this, 'renderPlacementSection' ),
			$this->pageSlug
		);

		// Show in placements
		add_settings_field(
			'show_in',
			__( 'Button Placements', 'html-social-share' ),
			array( $this, 'renderPlacementsField' ),
			$this->pageSlug,
			'placement_settings'
		);

		// Network Selection Section
		add_settings_section(
			'network_settings',
			__( 'Social Networks', 'html-social-share' ),
			array( $this, 'renderNetworkSection' ),
			$this->pageSlug
		);

		// Network checkboxes
		add_settings_field(
			'icons',
			__( 'Enabled Networks', 'html-social-share' ),
			array( $this, 'renderNetworksField' ),
			$this->pageSlug,
			'network_settings'
		);

		// Advanced Settings Section
		add_settings_section(
			'advanced_settings',
			__( 'Advanced Settings', 'html-social-share' ),
			array( $this, 'renderAdvancedSection' ),
			$this->pageSlug
		);

		// Google Analytics
		add_settings_field(
			'g_analytics',
			__( 'Google Analytics', 'html-social-share' ),
			array( $this, 'renderAnalyticsField' ),
			$this->pageSlug,
			'advanced_settings'
		);

		// Auto-hide buttons
		add_settings_field(
			'auto_hide_btn',
			__( 'Auto-Hide Buttons', 'html-social-share' ),
			array( $this, 'renderAutoHideField' ),
			$this->pageSlug,
			'advanced_settings'
		);

		// Nofollow links
		add_settings_field(
			'nofollow',
			__( 'Add Nofollow', 'html-social-share' ),
			array( $this, 'renderNofollowField' ),
			$this->pageSlug,
			'advanced_settings'
		);

		// Use port in URL
		add_settings_field(
			'use_port',
			__( 'Include Port in URL', 'html-social-share' ),
			array( $this, 'renderUsePortField' ),
			$this->pageSlug,
			'advanced_settings'
		);
	}

	/**
	 * Render settings page
	 */
	public function renderPage() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Show success message if settings saved
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				$this->pageSlug,
				'settings_updated',
				__( 'Settings saved successfully!', 'html-social-share' ),
				'success'
			);
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php settings_errors( $this->pageSlug ); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( $this->pageSlug );
				do_settings_sections( $this->pageSlug );
				submit_button( __( 'Save Settings', 'html-social-share' ) );
				?>
			</form>

			<div class="card">
				<h2><?php _e( 'Shortcode Usage', 'html-social-share' ); ?></h2>
				<p><?php _e( 'Use these shortcodes in your posts, pages, or widgets:', 'html-social-share' ); ?></p>
				<p><strong><?php _e( 'Modern shortcode (recommended):', 'html-social-share' ); ?></strong></p>
				<code>[html_social_share]</code><br>
				<code>[html_social_share iconset="flat" type="circle"]</code><br>
				<code>[html_social_share networks="facebook,twitter,linkedin"]</code>

				<p><strong><?php _e( 'Legacy shortcode (backward compatible):', 'html-social-share' ); ?></strong></p>
				<code>[zm_sh_btn]</code><br>
				<code>[zm_sh_btn iconset="flat" iconset_type="circle"]</code><br>
				<code>[zm_sh_btn icons="facebook,twitter,linkedin"]</code>
			</div>
		</div>
		<?php
	}

	/**
	 * Render general settings section
	 */
	public function renderGeneralSection() {
		echo '<p>' . esc_html__( 'Configure basic plugin settings.', 'html-social-share' ) . '</p>';
	}

	/**
	 * Render display settings section
	 */
	public function renderDisplaySection() {
		echo '<p>' . esc_html__( 'Choose how your share buttons look.', 'html-social-share' ) . '</p>';
	}

	/**
	 * Render placement settings section
	 */
	public function renderPlacementSection() {
		echo '<p>' . esc_html__( 'Control where share buttons appear on your site.', 'html-social-share' ) . '</p>';
	}

	/**
	 * Render network settings section
	 */
	public function renderNetworkSection() {
		echo '<p>' . esc_html__( 'Select which social networks to enable.', 'html-social-share' ) . '</p>';
	}

	/**
	 * Render advanced settings section
	 */
	public function renderAdvancedSection() {
		echo '<p>' . esc_html__( 'Advanced options for fine-tuning button behavior.', 'html-social-share' ) . '</p>';
	}

	/**
	 * Render title field
	 */
	public function renderTitleField() {
		$options = $this->optionsManager->getAll();
		$value   = isset( $options['title'] ) ? $options['title'] : '';
		?>
		<input type="text" 
		       name="<?php echo esc_attr( $this->optionName ); ?>[title]" 
		       value="<?php echo esc_attr( $value ); ?>" 
		       class="regular-text">
		<p class="description">
			<?php _e( 'Optional title to display above share buttons.', 'html-social-share' ); ?>
		</p>
		<?php
	}

	/**
	 * Render excludes field
	 */
	public function renderExcludesField() {
		$options = $this->optionsManager->getAll();
		$value   = isset( $options['excludes'] ) ? $options['excludes'] : '';
		?>
		<input type="text" 
		       name="<?php echo esc_attr( $this->optionName ); ?>[excludes]" 
		       value="<?php echo esc_attr( $value ); ?>" 
		       class="regular-text">
		<p class="description">
			<?php _e( 'Comma-separated list of post IDs to exclude. Example: 1,5,10', 'html-social-share' ); ?>
		</p>
		<?php
	}

	/**
	 * Render iconset field
	 */
	public function renderIconsetField() {
		$options  = $this->optionsManager->getAll();
		$value    = isset( $options['iconset'] ) ? $options['iconset'] : 'default';
		$iconsets = $this->iconRegistry->getAvailableIconsets();
		?>
		<select name="<?php echo esc_attr( $this->optionName ); ?>[iconset]">
			<?php foreach ( $iconsets as $iconset_id => $iconset_name ) : ?>
				<option value="<?php echo esc_attr( $iconset_id ); ?>" 
				        <?php selected( $value, $iconset_id ); ?>>
					<?php echo esc_html( $iconset_name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description">
			<?php _e( 'Choose the visual style for your share buttons.', 'html-social-share' ); ?>
		</p>
		<?php
	}

	/**
	 * Render placements field
	 */
	public function renderPlacementsField() {
		$options    = $this->optionsManager->getAll();
		$show_in    = isset( $options['show_in'] ) ? $options['show_in'] : array();
		$placements = array(
			'show_left'        => __( 'Left (Fixed)', 'html-social-share' ),
			'show_right'       => __( 'Right (Fixed)', 'html-social-share' ),
			'show_before_post' => __( 'Before Post Content', 'html-social-share' ),
			'show_after_post'  => __( 'After Post Content', 'html-social-share' ),
		);

		$types = array(
			'square' => __( 'Square', 'html-social-share' ),
			'circle' => __( 'Circle', 'html-social-share' ),
		);

		?>
		<table class="form-table">
			<?php foreach ( $placements as $key => $label ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $label ); ?></th>
					<td>
						<label>
							<input type="checkbox" 
							       name="<?php echo esc_attr( $this->optionName ); ?>[show_in][<?php echo esc_attr( $key ); ?>][enabled]" 
							       value="1" 
							       <?php checked( ! empty( $show_in[ $key ] ) ); ?>>
							<?php _e( 'Enable', 'html-social-share' ); ?>
						</label>
						&nbsp;&nbsp;
						<select name="<?php echo esc_attr( $this->optionName ); ?>[show_in][<?php echo esc_attr( $key ); ?>][type]">
							<?php foreach ( $types as $type_key => $type_label ) : ?>
								<option value="<?php echo esc_attr( $type_key ); ?>" 
								        <?php selected( isset( $show_in[ $key ] ) ? $show_in[ $key ] : 'square', $type_key ); ?>>
									<?php echo esc_html( $type_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
	}

	/**
	 * Render networks field
	 */
	public function renderNetworksField() {
		$options  = $this->optionsManager->getAll();
		$icons    = isset( $options['icons'] ) ? $options['icons'] : array();
		$networks = array(
			'facebook'    => __( 'Facebook', 'html-social-share' ),
			'twitter'     => __( 'Twitter', 'html-social-share' ),
			'linkedin'    => __( 'LinkedIn', 'html-social-share' ),
			'pinterest'   => __( 'Pinterest', 'html-social-share' ),
			'googlepluse' => __( 'Google+', 'html-social-share' ),
			'mail'        => __( 'Email', 'html-social-share' ),
		);

		?>
		<fieldset>
			<?php foreach ( $networks as $network_key => $network_label ) : ?>
				<label style="display: inline-block; min-width: 120px; margin-right: 10px;">
					<input type="checkbox" 
					       name="<?php echo esc_attr( $this->optionName ); ?>[icons][<?php echo esc_attr( $network_key ); ?>]" 
					       value="1" 
					       <?php checked( ! empty( $icons[ $network_key ] ) ); ?>>
					<?php echo esc_html( $network_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php
	}

	/**
	 * Render analytics field
	 */
	public function renderAnalyticsField() {
		$options = $this->optionsManager->getAll();
		$value   = isset( $options['g_analytics'] ) ? (bool) $options['g_analytics'] : false;
		?>
		<label>
			<input type="checkbox" 
			       name="<?php echo esc_attr( $this->optionName ); ?>[g_analytics]" 
			       value="1" 
			       <?php checked( $value ); ?>>
			<?php _e( 'Enable Google Analytics tracking for share clicks', 'html-social-share' ); ?>
		</label>
		<?php
	}

	/**
	 * Render auto-hide field
	 */
	public function renderAutoHideField() {
		$options = $this->optionsManager->getAll();
		$value   = isset( $options['auto_hide_btn'] ) ? (bool) $options['auto_hide_btn'] : false;
		?>
		<label>
			<input type="checkbox" 
			       name="<?php echo esc_attr( $this->optionName ); ?>[auto_hide_btn]" 
			       value="1" 
			       <?php checked( $value ); ?>>
			<?php _e( 'Auto-hide left/right fixed buttons when not hovering', 'html-social-share' ); ?>
		</label>
		<?php
	}

	/**
	 * Render nofollow field
	 */
	public function renderNofollowField() {
		$options = $this->optionsManager->getAll();
		$value   = isset( $options['nofollow'] ) ? (bool) $options['nofollow'] : false;
		?>
		<label>
			<input type="checkbox" 
			       name="<?php echo esc_attr( $this->optionName ); ?>[nofollow]" 
			       value="1" 
			       <?php checked( $value ); ?>>
			<?php _e( 'Add rel="nofollow" to share links', 'html-social-share' ); ?>
		</label>
		<?php
	}

	/**
	 * Render use port field
	 */
	public function renderUsePortField() {
		$options = $this->optionsManager->getAll();
		$value   = isset( $options['use_port'] ) ? (bool) $options['use_port'] : false;
		?>
		<label>
			<input type="checkbox" 
			       name="<?php echo esc_attr( $this->optionName ); ?>[use_port]" 
			       value="1" 
			       <?php checked( $value ); ?>>
			<?php _e( 'Include port number in shared URLs (for development sites)', 'html-social-share' ); ?>
		</label>
		<?php
	}

	/**
	 * Sanitize options before saving
	 *
	 * @param array $input Raw input from form
	 * @return array Sanitized options
	 */
	public function sanitizeOptions( $input ) {
		$sanitized = array();

		// Title
		if ( isset( $input['title'] ) ) {
			$sanitized['title'] = sanitize_text_field( $input['title'] );
		}

		// Excludes
		if ( isset( $input['excludes'] ) ) {
			$sanitized['excludes'] = sanitize_text_field( $input['excludes'] );
		}

		// Iconset
		if ( isset( $input['iconset'] ) ) {
			$sanitized['iconset'] = sanitize_key( $input['iconset'] );
		}

		// Placements
		if ( isset( $input['show_in'] ) && is_array( $input['show_in'] ) ) {
			$sanitized['show_in'] = array();
			foreach ( $input['show_in'] as $placement => $data ) {
				if ( ! empty( $data['enabled'] ) ) {
					$type = isset( $data['type'] ) ? sanitize_key( $data['type'] ) : 'square';
					$sanitized['show_in'][ sanitize_key( $placement ) ] = $type;
				}
			}
		}

		// Icons/Networks
		if ( isset( $input['icons'] ) && is_array( $input['icons'] ) ) {
			$sanitized['icons'] = array();
			foreach ( $input['icons'] as $network => $enabled ) {
				if ( $enabled ) {
					$sanitized['icons'][ sanitize_key( $network ) ] = '1';
				}
			}
		}

		// Boolean options
		$booleans = array( 'g_analytics', 'auto_hide_btn', 'nofollow', 'use_port' );
		foreach ( $booleans as $key ) {
			$sanitized[ $key ] = isset( $input[ $key ] ) ? (bool) $input[ $key ] : false;
		}

		return $sanitized;
	}
}
