#!/usr/bin/env php
<?php

declare( strict_types=1 );

$root = dirname( __DIR__ );
$autoload = $root . '/vendor/autoload.php';
if ( is_file( $autoload ) ) {
	require_once $autoload;
} else {
	spl_autoload_register(
		static function ( $class ) use ( $root ) {
			$prefix = 'Alimuzzaman\\HtmlSocialShareButtons\\';
			if ( 0 !== strpos( $class, $prefix ) ) {
				return;
			}
			$file = $root . '/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
			if ( is_file( $file ) ) {
				require_once $file;
			}
		}
	);
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		return $value;
	}
}
if ( ! function_exists( '__' ) ) {
	function __( $text ) {
		return $text;
	}
}
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $value ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) );
	}
}
if ( ! function_exists( 'sanitize_html_class' ) ) {
	function sanitize_html_class( $value ) {
		return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value );
	}
}
if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src, $dependencies, $version ) {
		$GLOBALS['hssb_appearance_enqueued_styles'][] = array( $handle, $src, $dependencies, $version );
	}
}

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\ButtonAppearance;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;

function hssb_appearance_fail( $message ) {
	fwrite( STDERR, $message . "\n" );
	exit( 1 );
}

final class HssbAppearanceSettingsRepository implements SettingsRepository {
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function load() {
		return $this->settings;
	}

	public function save( Settings $settings ) {
		$this->settings = $settings;
	}
}

function hssb_appearance_settings( $appearance, $autoHide = false ) {
	$defaults = SettingsDefaults::create();

	return new Settings(
		$defaults->title(),
		$defaults->iconSetId(),
		$defaults->defaultIconShape(),
		$defaults->placements(),
		$defaults->placementShapes(),
		$defaults->networkStates(),
		$defaults->shareTemplates(),
		$defaults->excludedContent(),
		$defaults->analyticsEnabled(),
		$autoHide,
		$defaults->preserveUrlPort(),
		$defaults->noFollow(),
		$defaults->profileLinks(),
		$defaults->profileLinkPlacements(),
		$defaults->showForCurrentUser(),
		$defaults->showForLoggedInUser(),
		$defaults->showForLoggedOutUser(),
		$appearance
	);
}

function hssb_appearance_facade( $root, $settings = null ) {
	$networks = ( new BuiltInNetworkProvider() )->createRegistry();
	$sets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );

	return new RenderFacade(
		$networks,
		$sets,
		new IconSetAssetResolver( $root, 'https://example.test/plugin' ),
		new ExtensionHooks(),
		null,
		null,
		null,
		$settings ? new HssbAppearanceSettingsRepository( $settings ) : null,
		null,
		'https://example.test/plugin/assets/frontend/button-appearance.css'
	);
}

$supported = array( 'legacy', 'minimal', 'framed', 'soft-shadow' );
if ( $supported !== ButtonAppearance::all() ) {
	hssb_appearance_fail( 'Button appearance allowlist changed unexpectedly.' );
}
foreach ( array( null, '', 'unknown', true, false, 1, array( 'minimal' ), new stdClass() ) as $invalid ) {
	if ( ButtonAppearance::LEGACY !== ButtonAppearance::normalize( $invalid ) ) {
		hssb_appearance_fail( 'Malformed button appearance did not fall back to Legacy.' );
	}
}

$codec = new OptionSettingsCodec();
foreach ( array( array(), array( 'button_appearance' => 'unknown' ), array( 'button_appearance' => array( 'minimal' ) ) ) as $stored ) {
	if ( ButtonAppearance::LEGACY !== $codec->decode( $stored )->buttonAppearance() ) {
		hssb_appearance_fail( 'Stored button appearance fallback failed.' );
	}
}
foreach ( $supported as $appearance ) {
	$stored = $codec->encode( hssb_appearance_settings( $appearance ), array( 'extension_key' => 'kept' ) );
	if ( $appearance !== $stored['button_appearance'] || 'kept' !== $stored['extension_key'] ) {
		hssb_appearance_fail( 'Button appearance storage round trip failed for ' . $appearance . '.' );
	}
}

$options = array(
	'iconset'      => 'default',
	'iconset_type' => 'square',
	'class'        => 'left',
	'icons'        => array( 'facebook' => 1 ),
	'url'          => 'https://example.test/article',
);
$context = new ShareContext( 'https://example.test/article', 'Example' );
$standaloneLegacy = hssb_appearance_facade( $root )->render( $options, 0, $context );
$storedLegacy = hssb_appearance_facade( $root, hssb_appearance_settings( 'legacy' ) )->render( $options, 0, $context );
if ( $standaloneLegacy->html() !== $storedLegacy->html() ) {
	hssb_appearance_fail( 'Legacy output is not byte-identical when the setting is absent and explicit.' );
}
if ( array( 'default' ) !== array_keys( $storedLegacy->stylesheets() ) || false !== strpos( $storedLegacy->html(), 'hssb-appearance--' ) ) {
	hssb_appearance_fail( 'Legacy output loaded or emitted a modern appearance asset.' );
}

foreach ( array( 'minimal', 'framed', 'soft-shadow' ) as $appearance ) {
	$outcome = hssb_appearance_facade( $root, hssb_appearance_settings( $appearance, true ) )->render( $options, 0, $context );
	if ( false === strpos( $outcome->html(), 'hssb-appearance--' . $appearance ) ) {
		hssb_appearance_fail( 'Safe wrapper modifier missing for ' . $appearance . '.' );
	}
	if ( false === strpos( $outcome->html(), 'hssb-rail--auto-hide' ) ) {
		hssb_appearance_fail( 'Modern automatic rail modifier missing for ' . $appearance . '.' );
	}
	if ( array( 'default', 'hssb-button-appearance' ) !== array_keys( $outcome->stylesheets() ) ) {
		hssb_appearance_fail( 'Modern stylesheet cascade order failed for ' . $appearance . '.' );
	}
}

$networks = ( new BuiltInNetworkProvider() )->createRegistry();
$sets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
$builtInPairs = 0;
foreach ( $sets->all() as $set ) {
	foreach ( $set->shapes() as $shape ) {
		$builtInPairs++;
		$pairOptions = array_replace(
			$options,
			array(
				'iconset'      => $set->id(),
				'iconset_type' => $shape,
			)
		);
		$outcome = hssb_appearance_facade( $root, hssb_appearance_settings( 'minimal' ) )->render( $pairOptions, 0, $context );
		if (
			false === strpos( $outcome->html(), 'hssb-appearance--minimal' ) ||
			array( $set->id(), 'hssb-button-appearance' ) !== array_keys( $outcome->stylesheets() )
		) {
			hssb_appearance_fail( 'Built-in icon set/shape appearance contract failed for ' . $set->id() . '/' . $shape . '.' );
		}
	}
}
if ( 11 !== $builtInPairs ) {
	hssb_appearance_fail( 'Expected eleven built-in icon set/shape pairs.' );
}

$GLOBALS['hssb_appearance_enqueued_styles'] = array();
$collector = new AssetCollector( 'fallback.css' );
$collector->collect( hssb_appearance_facade( $root, hssb_appearance_settings( 'minimal' ) )->render( $options, 0, $context ) );
$collector->enqueueStyles();
if ( array( 'social-share-default', 'hssb-button-appearance' ) !== array_column( $GLOBALS['hssb_appearance_enqueued_styles'], 0 ) ) {
	hssb_appearance_fail( 'Enqueued modern stylesheet did not follow the icon-pack stylesheet.' );
}

$legacyHashes = array(
	'iconset/default/style.css'                 => '1f36d950aaa3a10da1cc1cb8f7a0fec7e45a1ebcab50839505e39b6e943e2ccd',
	'iconset/flat/style.css'                    => '3efa6e10fe69c19853902780f74b3fabc824951e03e36d514b2c6322d5d7dd18',
	'iconset/long_shadow/style.css'             => 'd457626b9390437e5666a51b7ef8c01b272c03abcb7e26e04902f076e36164bb',
	'iconset/prajin/style.css'                  => '7dbe488a8e47ff5ca996b591aeeef57892f728b66678e9fb74d4c1378ed9898b',
	'assets/iconsets/bootstrap-solid/style.css' => '4729617c1391dda36dd7af1b15f668e7fc2a43fa8838d9079083063b7038242a',
	'assets/iconsets/tabler-outline/style.css'  => '31a24fc6719c7d15f7924848dee091157c42a1d200b848235c1cec413350f76a',
);
foreach ( $legacyHashes as $file => $hash ) {
	if ( $hash !== hash_file( 'sha256', $root . '/' . $file ) ) {
		hssb_appearance_fail( 'Legacy icon-pack CSS changed: ' . $file );
	}
}

$css = (string) file_get_contents( $root . '/assets/frontend/button-appearance.css' );
foreach ( array(
	'--hssb-target-size: 44px',
	'--hssb-gap: 8px',
	'background-origin: content-box',
	'background-size: contain',
	'@media (hover: hover) and (pointer: fine)',
	':focus:not(:focus-visible)',
	'@media (prefers-reduced-motion: reduce)',
	'@media (forced-colors: active)',
	'@media (max-width: 600px)',
	'.zmshbt-profile-separator',
	':focus-within',
) as $contract ) {
	if ( false === strpos( $css, $contract ) ) {
		hssb_appearance_fail( 'Modern CSS contract missing: ' . $contract );
	}
}

$sharedTargetSelector = '.zmshbt[class].hssb-appearance--minimal a,';
$framedTargetSelector = '.zmshbt[class].hssb-appearance--framed a,';
$softTargetSelector = '.zmshbt[class].hssb-appearance--soft-shadow a {';
$sharedTargetPosition = strpos( $css, $sharedTargetSelector );
$sharedTargetEnd = false === $sharedTargetPosition ? false : strpos( $css, '}', $sharedTargetPosition );
$framedTargetPosition = false === $sharedTargetEnd ? false : strpos( $css, $framedTargetSelector, $sharedTargetEnd );
$softTargetPosition = strpos( $css, $softTargetSelector, $framedTargetPosition + strlen( $framedTargetSelector ) );
$surfaceRuleEnd = false === $softTargetPosition ? false : strpos( $css, '}', $softTargetPosition );
$surfaceRule = false === $surfaceRuleEnd ? '' : substr( $css, $framedTargetPosition, $surfaceRuleEnd - $framedTargetPosition );
if (
	false === $sharedTargetPosition ||
	false === $framedTargetPosition ||
	false === $softTargetPosition ||
	false === strpos( $surfaceRule, 'border-width: 1px' ) ||
	false === strpos( $surfaceRule, 'border-color: var(--hssb-border)' )
) {
	hssb_appearance_fail( 'Framed and Soft shadow must override the shared zero border at matching specificity.' );
}

$softSurfaceSelector = '.zmshbt[class].hssb-appearance--soft-shadow a {';
$softSurfacePosition = strpos( $css, $softSurfaceSelector, $surfaceRuleEnd );
$softSurfaceEnd = false === $softSurfacePosition ? false : strpos( $css, '}', $softSurfacePosition );
$softSurfaceRule = false === $softSurfaceEnd ? '' : substr( $css, $softSurfacePosition, $softSurfaceEnd - $softSurfacePosition );
if (
	false === $softSurfacePosition ||
	false === strpos( $softSurfaceRule, 'background-color: var(--hssb-surface)' )
) {
	hssb_appearance_fail( 'Soft shadow must override the shared transparent background at matching specificity.' );
}

$specificityContracts = array(
	'Profile separators in modern side rails must override legacy margins.' => array(
		'.zmshbt.hssb-appearance--minimal.left .zmshbt-profile-separator,',
		'.zmshbt.hssb-appearance--soft-shadow.right .zmshbt-profile-separator {',
		'margin: 0',
	),
	'Keyboard focus must reveal automatic side rails.' => array(
		'.zmshbt.hssb-appearance--minimal.hssb-rail--auto-hide.left,',
		'.zmshbt[class].hssb-rail--auto-hide:focus-within',
		'transform: translateX(0)',
	),
	'Pointer hover must reveal automatic side rails.' => array(
		'.zmshbt.hssb-appearance--minimal.hssb-rail--auto-hide.right,',
		'@media (hover: hover) and (pointer: fine)',
		'.zmshbt[class].hssb-rail--auto-hide:hover',
		'transform: translateX(0)',
	),
	'Active state must outrank pointer hover while pressed.' => array(
		'@media (hover: hover) and (pointer: fine)',
		'.zmshbt[class].hssb-appearance--minimal a:active,',
		'transform: translateY(0) scale(.98)',
	),
	'Reduced motion must override the modern target transition.' => array(
		'@media (prefers-reduced-motion: reduce)',
		'.zmshbt[class].hssb-appearance--minimal a,',
		'transition: none',
	),
	'Reduced motion must override boosted hover and active transforms.' => array(
		'@media (prefers-reduced-motion: reduce)',
		'.zmshbt[class].hssb-appearance--minimal a:hover,',
		'.zmshbt[class].hssb-appearance--minimal a:active,',
		'transform: none',
	),
	'Reduced motion must override the automatic rail transition.' => array(
		'@media (prefers-reduced-motion: reduce)',
		'.zmshbt[class].hssb-rail--auto-hide',
		'transition: none',
	),
	'Reduced motion must override the appearance-specific rail transform.' => array(
		'@media (prefers-reduced-motion: reduce)',
		'.zmshbt[class].hssb-appearance--minimal.hssb-rail--auto-hide,',
		'transform: none',
	),
	'Forced colors must override the modern surface border and shadow.' => array(
		'@media (forced-colors: active)',
		'.zmshbt[class].hssb-appearance--framed a,',
		'border: 2px solid ButtonText',
		'box-shadow: none',
	),
	'Forced-color focus must override normal and focus-visible fallback shadows.' => array(
		'@media (forced-colors: active)',
		'.zmshbt[class].hssb-appearance--minimal a:focus,',
		'outline-color: Highlight',
		'box-shadow: none',
	),
	'Mobile side rails must override automatic rail transforms.' => array(
		'@media (max-width: 600px)',
		'.zmshbt[class].hssb-appearance--minimal.left,',
		'.zmshbt[class].hssb-appearance--soft-shadow.right {',
		'transform: none',
	),
);
foreach ( $specificityContracts as $failure => $needles ) {
	$position = 0;
	foreach ( $needles as $needle ) {
		$position = strpos( $css, $needle, $position );
		if ( false === $position ) {
			hssb_appearance_fail( $failure );
		}
		$position += strlen( $needle );
	}
}

echo "Button appearance storage, rendering, asset, Legacy parity, and CSS contracts passed.\n";
