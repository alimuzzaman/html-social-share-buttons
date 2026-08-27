<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Elementor;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\ButtonAppearance;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\BuilderLabels;

/**
 * Canonical Elementor implementation for the historic widget type.
 *
 * Elementor loads this class only after its Widget_Base class is available.
 */
class ElementorShareWidget extends \Elementor\Widget_Base {
	private static $configuredDependencies = array();
	private $renderer;
	private $settings;
	private $iconSets;
	private $networks;
	private $assets;
	private $config;

	/**
	 * Elementor creates a new widget instance for each stored element using its
	 * standard ($data, $args) constructor.  The registrar configures the
	 * canonical services once before it registers the widget type.  The
	 * dependency-injected form remains available for direct integration callers
	 * and contract tests.
	 */
	public function __construct(
		$rendererOrData = array(),
		$settingsOrArgs = null,
		$iconSets = null,
		$networks = null,
		$assets = null,
		$config = null
	) {
		$elementData = array();
		$elementArgs = array();
		if ( $rendererOrData instanceof RenderFacade ) {
			$this->setDependencies(
				$rendererOrData,
				$settingsOrArgs,
				$iconSets,
				$networks,
				$assets,
				$config
			);
		} else {
			$this->setConfiguredDependencies();
			$elementData = is_array( $rendererOrData ) ? $rendererOrData : array();
			$elementArgs = is_array( $settingsOrArgs ) ? $settingsOrArgs : array();
		}

		/* Elementor initializes its internal data in Widget_Base::__construct(). */
		if ( is_callable( array( get_parent_class( $this ), '__construct' ) ) ) {
			parent::__construct( $elementData, $elementArgs );
		}
	}

	public static function configureDependencies(
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		AssetCollector $assets,
		PluginConfig $config
	) {
		self::$configuredDependencies = array(
			'renderer' => $renderer,
			'settings' => $settings,
			'iconSets' => $iconSets,
			'networks' => $networks,
			'assets'   => $assets,
			'config'   => $config,
		);
	}

	private function setDependencies(
		$renderer,
		$settings,
		$iconSets,
		$networks,
		$assets,
		$config
	) {
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
		$this->config = $config;
	}

	private function setConfiguredDependencies() {
		$dependencies = self::$configuredDependencies;
		if (
			! isset( $dependencies['renderer'] ) ||
			! $dependencies['renderer'] instanceof RenderFacade ||
			! isset( $dependencies['settings'] ) ||
			! $dependencies['settings'] instanceof SettingsRepository ||
			! isset( $dependencies['iconSets'] ) ||
			! $dependencies['iconSets'] instanceof IconSetRegistry ||
			! isset( $dependencies['networks'] ) ||
			! $dependencies['networks'] instanceof NetworkRegistry ||
			! isset( $dependencies['assets'] ) ||
			! $dependencies['assets'] instanceof AssetCollector ||
			! isset( $dependencies['config'] ) ||
			! $dependencies['config'] instanceof PluginConfig
		) {
			throw new \RuntimeException( 'Elementor share widget dependencies have not been configured.' );
		}

		$this->setDependencies(
			$dependencies['renderer'],
			$dependencies['settings'],
			$dependencies['iconSets'],
			$dependencies['networks'],
			$dependencies['assets'],
			$dependencies['config']
		);
	}

	public function get_name() {
		return $this->config->elementorWidgetName();
	}

	public function get_title() {
		return esc_html__( 'Html Social Share', 'html-social-share-buttons' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Let Elementor load the canonical packs before it paints either the editor
	 * iframe or the public widget. Dependency discovery can run before display
	 * settings are hydrated, so this method deliberately does not read settings
	 * or render. WordPress deduplicates the registered handles.
	 */
	public function get_style_depends() {
		$handles = array();
		foreach ( $this->iconSets->all() as $iconSet ) {
			$handles[] = 'social-share-' . sanitize_key( $iconSet->id() );
		}
		if ( ButtonAppearance::LEGACY !== $this->settings->load()->buttonAppearance() ) {
			$handles[] = $this->config->buttonAppearanceStyleHandle();
		}

		return $handles;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Share buttons', 'html-social-share-buttons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'html-social-share-buttons' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Share this page', 'html-social-share-buttons' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'iconset',
			array(
				'label'   => esc_html__( 'Icon set', 'html-social-share-buttons' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->iconSetOptions(),
				'default' => 'inherit',
			)
		);

		$this->add_control(
			'iconset_type',
			array(
				'label'   => esc_html__( 'Button shape', 'html-social-share-buttons' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->shapeOptions(),
				'default' => 'square',
			)
		);

		$this->add_control(
			'icons',
			array(
				'label'       => esc_html__( 'Networks', 'html-social-share-buttons' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'description' => esc_html__(
					'Keep at least one network selected. Clearing all networks hides this widget on the frontend.',
					'html-social-share-buttons'
				),
				'options'     => $this->networkOptions(),
				'default'     => array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'profile_links_mode',
			array(
				'label'   => esc_html__( 'Profile links', 'html-social-share-buttons' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'inherit' => esc_html__( 'Show configured profile links', 'html-social-share-buttons' ),
					'none'    => esc_html__( 'Hide profile links in this widget', 'html-social-share-buttons' ),
				),
				'default' => 'inherit',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings = is_array( $settings ) ? $settings : array();
		$icons = $this->normalizeIconSelection(
			isset( $settings['icons'] ) ? $settings['icons'] : $this->defaultNetworks()
		);
		if ( empty( $icons ) ) {
			return;
		}

		$outcome = $this->renderer->render(
			array(
				'title'              => sanitize_text_field(
					$this->scalar( isset( $settings['title'] ) ? $settings['title'] : '', '' )
				),
				'iconset'            => $this->resolveIconSet(
					isset( $settings['iconset'] ) ? $settings['iconset'] : 'inherit'
				),
				'iconset_type'       => $this->resolveShape(
					isset( $settings['iconset_type'] ) ? $settings['iconset_type'] : 'square'
				),
				'icons'              => $icons,
				'class'              => $this->config->elementorWrapperClass(),
				'profile_links'      => $this->profileLinks( $settings ),
				'profile_links_mode' => $this->profileLinksMode(
					isset( $settings['profile_links_mode'] ) ? $settings['profile_links_mode'] : 'inherit'
				),
			)
		);
		$this->assets->collect( $outcome );
		if ( $this->isEditorPreview() ) {
			echo $this->assets->inlineIconStyles( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo wp_kses_post( $outcome->html() );
	}

	private function iconSetOptions() {
		$options = array(
			'inherit' => __( 'Inherit from plugin settings', 'html-social-share-buttons' ),
		);
		/*
		 * Elementor caches one control schema for both new and stored widgets.
		 * Keep the legacy value in that schema so old elements can hydrate it;
		 * the editor-only script hides it unless the current element selected it.
		 */
		foreach ( IconSetSelectionPolicy::choices( $this->iconSets, IconSetSelectionPolicy::LEGACY_DEFAULT_ID ) as $iconSet ) {
			$options[ $iconSet->id() ] = BuilderLabels::iconSet( $iconSet->id(), $iconSet->label() );
		}

		return $options;
	}

	private function shapeOptions() {
		$options = array();
		foreach ( $this->allShapes() as $shape ) {
			$options[ $shape ] = BuilderLabels::shape( $shape, ucfirst( $shape ) );
		}

		return $options;
	}

	private function allShapes() {
		$shapes = array();
		foreach ( $this->iconSets->all() as $iconSet ) {
			foreach ( $iconSet->shapes() as $shape ) {
				$shapes[ $shape ] = $shape;
			}
		}

		return empty( $shapes ) ? array( 'square' ) : array_values( $shapes );
	}

	private function networkOptions() {
		$options = array();
		foreach ( $this->networks->all() as $network ) {
			$options[ $network->id() ] = BuilderLabels::network( $network->id(), $network->label() );
		}

		return $options;
	}

	private function resolveIconSet( $iconSet ) {
		$iconSet = sanitize_key( $this->scalar( $iconSet, 'inherit' ) );
		if ( 'inherit' === $iconSet || ! $this->iconSets->has( $iconSet ) ) {
			return $this->settings->load()->iconSetId();
		}

		return $iconSet;
	}

	private function resolveShape( $shape ) {
		$shape = sanitize_key( $this->scalar( $shape, 'square' ) );

		return '' === $shape ? 'square' : $shape;
	}

	private function defaultNetworks() {
		return array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' );
	}

	private function profileLinks( array $settings ) {
		if ( array_key_exists( 'profile_links', $settings ) ) {
			return is_array( $settings['profile_links'] ) ? $settings['profile_links'] : array();
		}

		return $this->settings->load()->profileLinks();
	}

	private function profileLinksMode( $value ) {
		$mode = strtolower( $this->scalar( $value, 'inherit' ) );

		return in_array( $mode, array( 'inherit', 'none', 'custom' ), true ) ? $mode : 'inherit';
	}

	private function normalizeIconSelection( $icons ) {
		if ( ! is_array( $icons ) ) {
			return $this->defaultNetworks();
		}
		if ( empty( $icons ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $icons as $networkId ) {
			if ( ! is_scalar( $networkId ) ) {
				continue;
			}
			$networkId = sanitize_key( (string) $networkId );
			if ( 'twitter' === $networkId ) {
				$networkId = 'x';
			}
			if ( '' !== $networkId && $this->networks->has( $networkId ) ) {
				$normalized[ $networkId ] = 'on';
			}
		}

		return $normalized;
	}

	private function scalar( $value, $fallback ) {
		return is_scalar( $value ) ? (string) $value : (string) $fallback;
	}

	private function isEditorPreview() {
		if (
			! class_exists( '\\Elementor\\Plugin' ) ||
			! isset( \Elementor\Plugin::$instance ) ||
			! is_object( \Elementor\Plugin::$instance ) ||
			! isset( \Elementor\Plugin::$instance->editor ) ||
			! is_object( \Elementor\Plugin::$instance->editor ) ||
			! method_exists( \Elementor\Plugin::$instance->editor, 'is_edit_mode' )
		) {
			return false;
		}

		return (bool) \Elementor\Plugin::$instance->editor->is_edit_mode();
	}
}
