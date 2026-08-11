<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend;

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader;

/**
 * The canonical owner of automatic frontend placement and frontend assets.
 *
 * Public/legacy callbacks are intentionally expected to delegate into this
 * object.  It never reaches through a global runtime or compatibility layer.
 */
final class FrontendController {
	private $settings;
	private $renderer;
	private $contentPlacement;
	private $floatingPlacement;
	private $excludedContent;
	private $translations;
	private $assets;
	private $disabledMetaKey;
	private $optionCodec;
	private $legacyTextDomain;
	private $excluded = false;
	private $registered = false;
	private $legacyTranslationFilterRegistered = false;

	public function __construct(
		SettingsRepository $settings,
		RenderFacade $renderer,
		ContentPlacementComposer $contentPlacement,
		FloatingPlacementPlanner $floatingPlacement,
		ExcludedContentPolicy $excludedContent,
		TranslationLoader $translations,
		AssetCollector $assets,
		$disabledMetaKey,
		SettingsCodec $optionCodec = null,
		$legacyTextDomain = ''
	) {
		$this->settings = $settings;
		$this->renderer = $renderer;
		$this->contentPlacement = $contentPlacement;
		$this->floatingPlacement = $floatingPlacement;
		$this->excludedContent = $excludedContent;
		$this->translations = $translations;
		$this->assets = $assets;
		$this->disabledMetaKey = (string) $disabledMetaKey;
		$this->optionCodec = $optionCodec ? $optionCodec : new OptionSettingsCodec();
		$this->legacyTextDomain = (string) $legacyTextDomain;
	}

	public function registerHooks() {
		if ( $this->registered ) {
			return;
		}

		add_action( 'wp_footer', array( $this, 'footer' ) );
		add_action( 'init', array( $this, 'loadTranslations' ), 2 );
		add_action( 'wp', array( $this, 'detectExclusion' ) );

		$placements = $this->settings()->placements();
		if (
			! empty( $placements[ Placement::BEFORE_CONTENT ] ) ||
			! empty( $placements[ Placement::AFTER_CONTENT ] )
		) {
			add_filter( 'the_content', array( $this, 'filterContent' ) );
		}

		$this->registered = true;
	}

	public function loadTranslations() {
		if ( '' !== $this->legacyTextDomain ) {
			$this->translations->loadDomain( $this->legacyTextDomain );
		}
		$this->translations->load();
		if ( ! $this->legacyTranslationFilterRegistered ) {
			add_filter( 'gettext', array( $this, 'translateLegacyDomain' ), 10, 3 );
			$this->legacyTranslationFilterRegistered = true;
		}
	}

	/**
	 * An installed historic catalog may fill only canonical strings WordPress
	 * has not already translated for the current site.
	 */
	public function translateLegacyDomain( $translation, $text, $domain ) {
		if (
			$this->translations->textDomain() !== $domain ||
			$translation !== $text ||
			'' === $this->legacyTextDomain
		) {
			return $translation;
		}

		$legacyTranslation = get_translations_for_domain( $this->legacyTextDomain )
			->translate( $text );

		return $legacyTranslation !== $text ? $legacyTranslation : $translation;
	}

	public function detectExclusion() {
		$this->excluded = false;
		$post = $this->currentPost();
		if ( ! $post ) {
			return;
		}

		$settings = $this->settings();
		if (
			$this->excludedContent->matches(
				$post->ID,
				isset( $post->post_name ) ? $post->post_name : '',
				isset( $post->post_title ) ? $post->post_title : '',
				$settings->excludedContent()
			)
		) {
			$this->excluded = true;
			return;
		}

		$this->excluded = 'on' === get_post_meta( $post->ID, $this->disabledMetaKey, true );
	}

	public function filterContent( $content ) {
		return $this->filterContentWithSettings( $content, $this->settings() );
	}

	/**
	 * Compose automatic placement from the durable option shape supplied by a
	 * public compatibility adapter without making the canonical controller
	 * depend on an adapter-owned runtime.
	 */
	public function filterContentWithOptions( $content, array $options ) {
		return $this->filterContentWithOptionsAndAssets( $content, $options, null );
	}

	/**
	 * Canonical rendering path for an isolated public-object render session.
	 */
	public function filterContentWithOptionsAndAssets( $content, array $options, AssetCollector $assets = null ) {
		return $this->filterContentWithSettings(
			$content,
			$this->optionCodec->decode( $options ),
			$assets,
			$options
		);
	}

	private function filterContentWithSettings( $content, Settings $settings, AssetCollector $assets = null, array $fallbackOptions = array() ) {
		if ( $this->excluded ) {
			return $content;
		}

		return $this->contentPlacement->compose(
			$content,
			$settings,
			function ( $placement ) use ( $settings, $assets, $fallbackOptions ) {
				return $this->renderWithOptions(
					$this->placementOptionsFor( $settings, $placement, 'in_widget' ),
					$this->currentPostId(),
					$assets,
					$fallbackOptions
				);
			},
			is_singular()
		);
	}

	public function footer() {
		if ( is_admin() || $this->excluded ) {
			return;
		}

		$settings = $this->settings();
		if ( $settings->analyticsEnabled() ) {
			echo $this->analyticsScript( ! empty( $settings->profileLinks() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		foreach ( $this->floatingPlacement->enabled( $settings ) as $placement ) {
			echo wp_kses_post( $this->renderPlacement( $placement, $placement ) );
		}

		$this->assets->enqueueStyles();
		echo $this->assets->inlineIconStyles( $settings->autoHideEnabled() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Public rendering entry point for thin integration and legacy bridges.
	 */
	public function render( array $options, $contextPostId = 0 ) {
		return $this->renderWithOptions( $options, $contextPostId );
	}

	/**
	 * Render through a supplied request-local collector when a public object
	 * owns an isolated render session; normal runtime calls omit it.
	 */
	public function renderWithOptions( array $options, $contextPostId = 0, AssetCollector $assets = null, array $fallbackOptions = array() ) {
		if ( $this->excluded ) {
			return null;
		}

		if ( ! array_key_exists( 'profile_links', $options ) && isset( $fallbackOptions['profile_links'] ) && is_array( $fallbackOptions['profile_links'] ) ) {
			$options['profile_links'] = $fallbackOptions['profile_links'];
		} elseif ( ! array_key_exists( 'profile_links', $options ) ) {
			$options['profile_links'] = $this->settings()->profileLinks();
		}
		$outcome = $this->renderer->render( $options, (int) $contextPostId );
		( $assets ? $assets : $this->assets )->collect( $outcome );

		return $outcome->html();
	}

	public function renderPlacement( $placement, $className ) {
		return $this->render(
			$this->placementOptions( $placement, $className ),
			$this->currentPostId()
		);
	}

	public function createAssetCollector() {
		return $this->assets->fresh();
	}

	/**
	 * Generate footer output from an isolated option/asset session. The normal
	 * request footer remains below and retains its request-scoped collector.
	 */
	public function footerWithOptions( array $options, AssetCollector $assets ) {
		if ( is_admin() || $this->excluded ) {
			return '';
		}

		$settings = $this->optionCodec->decode( $options );
		$html = $settings->analyticsEnabled()
			? $this->analyticsScript( ! empty( $settings->profileLinks() ) )
			: '';
		foreach ( $this->floatingPlacement->enabled( $settings ) as $placement ) {
			/* The public footer historically passed each group through wp_kses_post(). */
			$html .= wp_kses_post(
				$this->renderWithOptions(
					$this->placementOptionsFor( $settings, $placement, $placement ),
					$this->currentPostId(),
					$assets,
					$options
				)
			);
		}

		$assets->enqueueStyles();

		return $html . $assets->historicalInlineIconStyles( $settings->autoHideEnabled() );
	}

	public function enqueueCollectedAssets( AssetCollector $assets ) {
		$assets->enqueueStyles();
	}

	public function historicalCollectedIconStyles( $autoHideEnabled, AssetCollector $assets ) {
		return $assets->historicalInlineIconStyles( $autoHideEnabled );
	}

	public function isExcluded() {
		return $this->excluded;
	}

	public function assets() {
		return $this->assets;
	}

	private function placementOptions( $placement, $className ) {
		return $this->placementOptionsFor( $this->settings(), $placement, $className );
	}

	private function placementOptionsFor( Settings $settings, $placement, $className ) {
		$renderPlacement = array(
			Placement::LEFT           => RenderPlacement::FLOATING_LEFT,
			Placement::RIGHT          => RenderPlacement::FLOATING_RIGHT,
			Placement::BEFORE_CONTENT => RenderPlacement::BEFORE_CONTENT,
			Placement::AFTER_CONTENT  => RenderPlacement::AFTER_CONTENT,
		);
		$showOn = array(
			Placement::LEFT           => 'show_left',
			Placement::RIGHT          => 'show_right',
			Placement::BEFORE_CONTENT => 'show_before_post',
			Placement::AFTER_CONTENT  => 'show_after_post',
		);
		$shapes = $settings->placementShapes();

		return array(
			'title'           => $settings->title(),
			'iconset'         => $settings->iconSetId(),
			'iconset_type'    => isset( $shapes[ $placement ] )
				? $shapes[ $placement ] : $settings->defaultIconShape(),
			'icons'           => $this->enabledNetworks( $settings ),
			'share_templates' => $settings->shareTemplates(),
			'nofollow'        => $settings->noFollow(),
			'profile_links'   => $settings->profileLinks(),
			'class'           => (string) $className,
			'show_on'         => isset( $showOn[ $placement ] ) ? $showOn[ $placement ] : 'show_left',
			'placement'       => isset( $renderPlacement[ $placement ] )
				? $renderPlacement[ $placement ] : RenderPlacement::PHP_API,
		);
	}

	private function settings() {
		return $this->settings->load();
	}

	private function enabledNetworks( Settings $settings ) {
		$enabled = array();
		foreach ( $settings->networkStates() as $networkId => $isEnabled ) {
			/*
			 * The stored option's network keys are the historical automatic
			 * placement selection. Some pre-rewrite settings deliberately retain
			 * a false value for a key, and the old automatic renderer still drew
			 * that keyed network. Retain the key while the domain model carries
			 * the value for settings UI and validation.
			 */
			$enabled[ (string) $networkId ] = $isEnabled;
		}

		return $enabled;
	}

	private function currentPost() {
		$object = get_queried_object();
		if ( is_object( $object ) && isset( $object->ID ) && (int) $object->ID > 0 ) {
			return $object;
		}

		$postId = get_queried_object_id();

		return $postId ? get_post( $postId ) : null;
	}

	private function currentPostId() {
		$post = $this->currentPost();

		return $post ? (int) $post->ID : 0;
	}

	private function analyticsScript( $hasProfileLinks ) {
		$selector = $hasProfileLinks
			? '.zmshbt a:not(.zmshbt-profile-link)'
			: '.zmshbt a';

		return "\n\t\t\t\t<script>\n\t\t\t\tjQuery(document).ready(function($){\n\t\t\t\t\tvar _gaq = _gaq || [];\n\t\t\t\t\tjQuery('" . esc_js( $selector ) . "').on('click', function(event){\n\t\t\t\t\t\tvar _gaq = _gaq || [];\n\t\t\t\t\t\tswitch(this.className){\n\t\t\t\t\t\t\tcase 'googlepluse':\n\t\t\t\t\t\t\t\taction = '+1';\n\t\t\t\t\t\t\tcase 'twitter':\n\t\t\t\t\t\t\t\taction = 'Tweet';\n\t\t\t\t\t\t\tcase 'mail':\n\t\t\t\t\t\t\t\taction = 'Mail';\n\t\t\t\t\t\t\tdefault :\n\t\t\t\t\t\t\t\taction = 'Share';\n\t\t\t\t\t\t}\n\t\t\t\t\t\t_gaq.push(['_trackSocial', this.className, action]);\n\t\t\t\t\t\tconsole.log(action);\n\t\t\t\t\t});\n\t\t\t\t});\n\t\t\t\t</script>\n\t\t\t";
	}
}
