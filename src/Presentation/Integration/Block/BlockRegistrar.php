<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Block;

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;

/**
 * Registers dynamic editor blocks from their metadata and delegates only
 * frontend rendering to the canonical presentation facade.
 */
final class BlockRegistrar {
	private $pluginRoot;
	private $renderer;
	private $settings;
	private $iconSets;
	private $assets;
	private $networks;
	private $assetCollector;
	private $config;

	public function __construct(
		$pluginRoot,
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		IconSetAssetResolver $assets,
		NetworkRegistry $networks,
		AssetCollector $assetCollector,
		PluginConfig $config
	) {
		$this->pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->assets = $assets;
		$this->networks = $networks;
		$this->assetCollector = $assetCollector;
		$this->config = $config;
	}

	public function registerHooks() {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'init', array( $this, 'registerBlocks' ) );
		}
	}

	public function register() {
		$this->registerHooks();
	}

	public function registerBlocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$this->registerEditorScript(
			$this->config->shareBlockEditorHandle(),
			'social-share',
			'hssbShareBlock',
			$this->editorConfig()
		);
		$this->registerEditorScript(
			$this->config->socialLinksBlockEditorHandle(),
			'social-links',
			'hssbSocialLinksBlock',
			$this->editorConfig()
		);

		$this->registerMetadata(
			$this->pluginRoot . '/block.json',
			array( $this, 'renderShareBlock' ),
			$this->config->shareBlockEditorHandle()
		);
		$this->registerMetadata(
			$this->pluginRoot . '/blocks/social-links/block.json',
			array( $this, 'renderSocialLinksBlock' ),
			$this->config->socialLinksBlockEditorHandle()
		);
	}

	/**
	 * Historical public callback shape, for bridges that still invoke it.
	 */
	public function render( $attributes, $contextPostId = 0 ) {
		return $this->renderShare( $attributes, $contextPostId );
	}

	public function renderShareBlock( $attributes, $content = '', $block = null ) {
		return $this->renderShare( $attributes, $this->contextPostId( $block ) );
	}

	public function renderSocialLinksBlock( $attributes, $content = '', $block = null ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$settings = $this->settings->load();
		$mode = $this->key(
			isset( $attributes['profile_links_mode'] ) ? $attributes['profile_links_mode'] : 'inherit',
			'inherit'
		);
		$profileLinks = array();
		if ( 'inherit' === $mode ) {
			$profileLinks = $settings->profileLinks();
		} elseif ( 'custom' === $mode ) {
			$profileLinks = $this->profileLinks(
				isset( $attributes['profile_links'] ) && is_array( $attributes['profile_links'] )
					? $attributes['profile_links']
					: array()
			);
		}

		if ( empty( $profileLinks ) ) {
			return '';
		}

		$options = array(
			'title'         => $this->text(
				isset( $attributes['title'] ) ? $attributes['title'] : '',
				''
			),
			'iconset'       => $this->resolvedIconSet(
				isset( $attributes['iconset'] ) ? $attributes['iconset'] : 'inherit'
			),
			'iconset_type'  => $this->key(
				isset( $attributes['iconset_type'] ) ? $attributes['iconset_type'] : 'square',
				'square'
			),
			'icons'         => array(),
			'class'         => 'in_block',
			'profile_links' => $profileLinks,
			'profiles_only' => true,
			'show_heading'  => true,
		);

		return $this->renderOutcome( $options, $this->contextPostId( $block ) );
	}

	public function builderIconSetAssets() {
		$assets = array();
		foreach ( $this->iconSets->all() as $iconSet ) {
			$assets[ $iconSet->id() ] = array(
				'types' => $iconSet->shapes(),
				'icons' => array(),
			);
			foreach ( $iconSet->iconFiles() as $networkId => $unused ) {
				foreach ( $iconSet->shapes() as $shape ) {
					$assets[ $iconSet->id() ]['icons'][ $networkId ][ $shape ] =
						$this->assets->iconUrl( $iconSet, $shape, $networkId );
				}
			}
		}

		return $assets;
	}

	private function renderShare( $attributes, $contextPostId ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$icons = isset( $attributes['icons'] ) && is_array( $attributes['icons'] )
			? $attributes['icons']
			: array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' );
		if ( empty( $icons ) ) {
			return '';
		}

		$settings = $this->settings->load();
		$options = array(
			'title'         => $this->text(
				isset( $attributes['title'] ) ? $attributes['title'] : 'Share this page',
				'Share this page'
			),
			'iconset'       => $this->resolvedIconSet(
				isset( $attributes['iconset'] ) ? $attributes['iconset'] : 'inherit'
			),
			'iconset_type'  => $this->key(
				isset( $attributes['iconset_type'] ) ? $attributes['iconset_type'] : 'square',
				'square'
			),
			'icons'         => $this->icons( $icons ),
			'class'         => 'in_block',
			'profile_links' => $settings->profileLinks(),
		);

		return $this->renderOutcome( $options, $contextPostId );
	}

	private function registerMetadata( $metadataPath, $callback, $editorScript ) {
		if ( ! file_exists( $metadataPath ) ) {
			return;
		}

		$args = array( 'render_callback' => $callback );
		if ( function_exists( 'register_block_type_from_metadata' ) ) {
			call_user_func( 'register_block_type_from_metadata', $metadataPath, $args );

			return;
		}

		$metadata = json_decode( (string) file_get_contents( $metadataPath ), true );
		if ( ! is_array( $metadata ) || empty( $metadata['name'] ) ) {
			return;
		}
		register_block_type(
			$metadata['name'],
			array_merge(
				$args,
				array(
					'attributes'    => isset( $metadata['attributes'] ) ? $metadata['attributes'] : array(),
					'editor_script' => $editorScript,
				)
			)
		);
	}

	private function renderOutcome( array $options, $contextPostId ) {
		$outcome = $this->renderer->render( $options, $contextPostId );
		$this->assetCollector->collect( $outcome );

		return $outcome->html();
	}

	private function registerEditorScript( $handle, $entry, $objectName, array $config ) {
		$assetPath = $this->pluginRoot . '/build/' . $entry . '.asset.php';
		$asset = file_exists( $assetPath ) ? require $assetPath : array();
		$dependencies = isset( $asset['dependencies'] ) && is_array( $asset['dependencies'] )
			? $asset['dependencies']
			: array();
		$dependencies = array_values(
			array_unique(
				array_merge(
					array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ),
					$dependencies
				)
			)
		);
		$scriptPath = $this->pluginRoot . '/build/' . $entry . '.js';
		$version = isset( $asset['version'] ) ? $asset['version'] : ( file_exists( $scriptPath ) ? filemtime( $scriptPath ) : '2.2.6' );

		wp_register_script(
			$handle,
			plugins_url( 'build/' . $entry . '.js', $this->pluginRoot . '/html-social-share.php' ),
			$dependencies,
			$version,
			true
		);
		wp_localize_script( $handle, $objectName, $config );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'html-social-share-buttons', $this->pluginRoot . '/languages' );
		}
	}

	private function editorConfig() {
		$settings = $this->settings->load();
		$networks = array();
		foreach ( $this->networks->all() as $network ) {
			$networks[] = array(
				'id'    => $network->id(),
				'label' => $this->networkLabel( $network->id(), $network->label() ),
			);
		}

		$iconSets = array(
			'inherit' => __( 'Inherit from plugin settings', 'html-social-share-buttons' ),
		);
		foreach ( $this->iconSets->all() as $iconSet ) {
			$iconSets[ $iconSet->id() ] = $this->iconSetLabel( $iconSet->id(), $iconSet->label() );
		}

		return array(
			'networks'         => $networks,
			'iconsets'         => $iconSets,
			'iconsetAssets'    => $this->builderIconSetAssets(),
			'inheritedIconset' => $this->resolvedIconSet( 'inherit' ),
			'profileLinks'     => $settings->profileLinks(),
		);
	}

	private function resolvedIconSet( $value ) {
		$value = $this->key( $value, 'inherit' );
		if ( 'inherit' === $value ) {
			$value = $this->settings->load()->iconSetId();
		}

		return $this->iconSets->has( $value ) ? $value : 'default';
	}

	private function icons( array $icons ) {
		$normalized = array();
		foreach ( $icons as $key => $value ) {
			$networkId = is_int( $key ) ? $value : $key;
			if ( ! is_scalar( $networkId ) ) {
				continue;
			}
			$networkId = $this->key( $networkId, '' );
			$networkId = 'twitter' === $networkId ? 'x' : $networkId;
			if ( '' !== $networkId ) {
				$normalized[ $networkId ] = 'on';
			}
		}

		return $normalized;
	}

	private function profileLinks( array $profileLinks ) {
		$normalized = array();
		foreach ( $profileLinks as $networkId => $url ) {
			$networkId = $this->key( $networkId, '' );
			$networkId = 'twitter' === $networkId ? 'x' : $networkId;
			if ( '' === $networkId || ! is_scalar( $url ) ) {
				continue;
			}
			$url = trim( (string) $url );
			if ( 'mail' === $networkId ) {
				if ( 0 !== stripos( $url, 'mailto:' ) ) {
					continue;
				}
				$address = substr( $url, 7 );
				if ( false === strpos( $address, '?' ) && false === strpos( $address, '#' ) && function_exists( 'is_email' ) && is_email( rawurldecode( $address ) ) ) {
					$normalized[ $networkId ] = 'mailto:' . rawurldecode( $address );
				}
				continue;
			}
			if ( function_exists( 'esc_url_raw' ) ) {
				$url = esc_url_raw( $url, array( 'https' ) );
			}
			if ( 0 === strpos( strtolower( $url ), 'https://' ) ) {
				$normalized[ $networkId ] = $url;
			}
		}

		return $normalized;
	}

	private function contextPostId( $block ) {
		return is_object( $block ) && isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;
	}

	private function text( $value, $fallback ) {
		$value = is_scalar( $value ) ? (string) $value : (string) $fallback;

		return function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : $value;
	}

	private function key( $value, $fallback ) {
		$value = is_scalar( $value ) ? (string) $value : (string) $fallback;

		return function_exists( 'sanitize_key' ) ? sanitize_key( $value ) : strtolower( $value );
	}

	private function networkLabel( $id, $fallback ) {
		switch ( (string) $id ) {
			case 'facebook':
				return __( 'Facebook', 'html-social-share-buttons' );
			case 'x':
				return __( 'X', 'html-social-share-buttons' );
			case 'linkedin':
				return __( 'LinkedIn', 'html-social-share-buttons' );
			case 'pinterest':
				return __( 'Pinterest', 'html-social-share-buttons' );
			case 'telegram':
				return __( 'Telegram', 'html-social-share-buttons' );
			case 'bluesky':
				return __( 'Bluesky', 'html-social-share-buttons' );
			case 'mail':
				return __( 'Email', 'html-social-share-buttons' );
			default:
				return (string) $fallback;
		}
	}

	private function iconSetLabel( $id, $fallback ) {
		switch ( (string) $id ) {
			case 'default':
				return __( 'Default', 'html-social-share-buttons' );
			case 'flat':
				return __( 'Flat', 'html-social-share-buttons' );
			case 'long-shadows':
				return __( 'Long Shadows', 'html-social-share-buttons' );
			case 'prajin':
				return __( 'Prajin', 'html-social-share-buttons' );
			case 'bootstrap-solid':
				return __( 'Bootstrap Solid', 'html-social-share-buttons' );
			case 'tabler-outline':
				return __( 'Tabler Outline', 'html-social-share-buttons' );
			default:
				return (string) $fallback;
		}
	}
}
