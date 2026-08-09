<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Runtime;

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings\LegacySettingsMapper;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;

class SocialShareAdapter {
	public $iconset;
	public $iconsets;
	public $excluded = false;
	public $options;

	private $schemas;
	private $icons;
	private $printed_icons;
	private $stylesheets;
	private $settingsMapper;

	public function __construct() {
		global $zm_sh_default_options;

		$this->options = LegacyRuntime::settings()->runtime( $zm_sh_default_options );
		$this->settingsMapper = new LegacySettingsMapper();

		$this->iconsets = new \zm_sh_iconset();
		$this->iconset = $this->iconsets->get_current_iconset();

		add_action( 'wp_footer', array( $this, 'footer' ) );
		add_action( 'init', array( $this, 'plugins_loaded' ), 2 );
		if (
			isset( $this->options['show_after_post'] ) && $this->options['show_after_post'] ||
			isset( $this->options['show_before_post'] ) && $this->options['show_before_post']
		) {
			add_filter( 'the_content', array( $this, 'filter_the_content' ) );
		}
		add_action( 'wp', array( $this, 'wp' ) );
	}

	public function wp() {
		global $post;

		if ( ! is_object( $post ) || empty( $post->ID ) ) {
			return;
		}

		$excludes = ! empty( $this->options['excludes'] ) ? $this->options['excludes'] : '';
		if ( zm_sh_post_is_excluded( $post, $excludes ) ) {
			$this->excluded = true;
			return;
		}

		if ( get_post_meta( $post->ID, '_zm_sh_disable_share', true ) == 'on' ) {
			$this->excluded = true;
		}
	}

	public function plugins_loaded() {
		$translations = LegacyRuntime::plugin()->translations();
		$languagePath = $translations->relativeLanguagePath();

		load_plugin_textdomain( 'zm-sh', false, $languagePath );
		$translations->load();
		add_filter( 'gettext', array( $this, 'translate_legacy_domain' ), 10, 3 );
	}

	public function translate_legacy_domain( $translation, $text, $domain ) {
		if ( 'html-social-share-buttons' !== $domain || $translation !== $text ) {
			return $translation;
		}

		$legacyTranslation = get_translations_for_domain( 'zm-sh' )->translate( $text );

		return $legacyTranslation !== $text ? $legacyTranslation : $translation;
	}

	public function filter_the_content( $content ) {
		return LegacyRuntime::contentPlacement()->compose(
			$content,
			$this->runtimeSettings(),
			function ( $placement ) {
				return $this->zm_sh_btn(
					$this->legacyPlacementOptions( $placement, 'in_widget' )
				);
			},
			is_singular()
		);
	}

	public function footer() {
		if ( is_admin() || $this->excluded == true ) {
			return;
		}
		$options = $this->options;

		if ( isset( $options['g_analytics'] ) && $options['g_analytics'] ) {
			$shareSelector = ! empty( $options['profile_links'] )
				? '.zmshbt a:not(.zmshbt-profile-link)'
				: '.zmshbt a';
			echo "
				<script>
				jQuery(document).ready(function($){
					var _gaq = _gaq || [];
					jQuery('" . esc_js( $shareSelector ) . "').on('click', function(event){
						var _gaq = _gaq || [];
						switch(this.className){
							case 'googlepluse':
								action = '+1';
							case 'twitter':
								action = 'Tweet';
							case 'mail':
								action = 'Mail';
							default :
								action = 'Share';
						}
						_gaq.push(['_trackSocial', this.className, action]);
						console.log(action);
					});
				});
				</script>
			";
		}
		foreach ( LegacyRuntime::floatingPlacement()->enabled( $this->runtimeSettings() ) as $placement ) {
			echo wp_kses_post(
				$this->zm_sh_btn(
					$this->legacyPlacementOptions( $placement, $placement )
				)
			);
		}

		$this->register_styles();
		$this->icon_styles();
	}

	public function register_styles() {
		if ( is_array( $this->stylesheets ) ) {
			foreach ( $this->stylesheets as $id => $stylesheet ) {
				wp_enqueue_style( 'social-share-' . sanitize_key( $id ), $stylesheet, array(), '2.2.4' );
			}
			return;
		}

		wp_enqueue_style(
			'social-share-default',
			plugins_url(
				'iconset/default/style.css',
				LegacyRuntime::pluginRoot() . '/html-social-share.php'
			),
			array(),
			'2.2.4'
		);
	}

	public function icon_styles() {
		if ( ! is_array( $this->printed_icons ) ) {
			return;
		}

		echo '<style>';
		foreach ( $this->printed_icons as $iconSet ) {
			$iconSetId = isset( $iconSet['iconset_id'] ) ? $iconSet['iconset_id'] : '';
			$iconSetType = isset( $iconSet['iconset_type'] ) ? $iconSet['iconset_type'] : '';
			$iconSetUrl = isset( $iconSet['iconset_url'] ) ? $iconSet['iconset_url'] : '';
			$className = isset( $iconSet['class'] ) ? $iconSet['class'] : '';
			$image = isset( $iconSet['image'] ) ? $iconSet['image'] : '';
			echo "
			.zmshbt." . esc_attr( $iconSetId ) . '.' . esc_attr( $iconSetType ) . ' .' . esc_attr( $className ) . " {
					background-image:url('" . esc_url( $iconSetUrl . $iconSetType . '/' . $image ) . "');
			}
			";
		}
		if ( empty( $this->options['auto_hide_btn'] ) ) {
			echo "
				.zmshbt.left{
					left: 0 !important;
				}
				.zmshbt.right {
					right: 0 !important;
				}
			";
		}
		echo '</style>';
	}

	public function zm_sh_btn( $instance = '' ) {
		if ( $this->excluded == true ) {
			return null;
		}
		$options = is_array( $instance ) && ! empty( $instance )
			? $instance
			: ( is_array( $this->options ) ? $this->options : array() );
		if (
			! array_key_exists( 'profile_links', $options ) &&
			is_array( $this->options ) &&
			isset( $this->options['profile_links'] ) &&
			is_array( $this->options['profile_links'] )
		) {
			$options['profile_links'] = $this->options['profile_links'];
		}
		$outcome = LegacyRuntime::renderer()->render( $options, $this->iconsets );

		$this->stylesheets = array_merge(
			is_array( $this->stylesheets ) ? $this->stylesheets : array(),
			$outcome->stylesheets()
		);
		$this->printed_icons = array_merge(
			is_array( $this->printed_icons ) ? $this->printed_icons : array(),
			$outcome->printedIcons()
		);

		return $outcome->html();
	}

	private function runtimeSettings() {
		return $this->settingsMapper->fromArray(
			is_array( $this->options ) ? $this->options : array()
		);
	}

	private function legacyPlacementOptions( $placement, $className ) {
		$showOn = array(
			Placement::LEFT           => 'show_left',
			Placement::RIGHT          => 'show_right',
			Placement::BEFORE_CONTENT => 'show_before_post',
			Placement::AFTER_CONTENT  => 'show_after_post',
		);
		$options = is_array( $this->options ) ? $this->options : array();
		$options['class'] = $className;
		$options['show_on'] = isset( $showOn[ $placement ] ) ? $showOn[ $placement ] : '';

		return $options;
	}
}
