<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\HtmlRenderer;

function __( $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return esc_attr( $value ); }
function esc_url( $value ) { return esc_attr( $value ); }
function esc_attr__( $value ) { return esc_attr( $value ); }

function native_check( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

$networks = ( new BuiltInNetworkProvider() )->createRegistry();
$sets = ( new ManifestIconSetProvider( dirname( __DIR__ ) . '/resources/iconsets' ) )->createRegistry( $networks );
$builder = new BuildShareButtons( $networks, $sets, new ResolveShareUrl() );
$renderer = new HtmlRenderer();
$url = 'https://example.test/caf%C3%A9/?a=1&b=two%20words#part';
$context = new ShareContext( $url, 'Article "title" & café', '', 'https://example.test/image.jpg?width=500&label=caf%C3%A9%20image' );
native_check( $networks->get( 'native' )->isNativeAction(), 'Native has its own action kind.' );
native_check( ! $networks->get( 'native' )->isCopyAction(), 'Native is not Copy link.' );
native_check( ! $networks->get( 'native' )->enabledByDefault(), 'Native must stay opt-in.' );
native_check( ! isset( SettingsDefaults::create()->networkStates()['native'] ), 'Never-saved legacy defaults must omit native.' );

foreach ( $sets->all() as $set ) {
	foreach ( $set->shapes() as $shape ) {
		foreach ( array( array( 'native' ), array( 'native', 'copy' ) ) as $ids ) {
			$request = new RenderRequest( $set->id(), $shape, RenderPlacement::SHORTCODE, '', $ids, array(), '', false, array( 'native' => 'https://profile.test/' ), false, 'legacy', false, true );
			$result = $builder->build( $request, $context );
			$html = $renderer->render( $request, $result );
			native_check( array() === $result->profileLinks(), 'Native must never render as a profile.' );
			native_check( false !== strpos( $html, "data-hssb-native-share='1'" ), 'Native marker missing for ' . $set->id() );
			native_check( false !== strpos( $html, "data-hssb-native-title='Article &quot;title&quot; &amp; café'" ), 'Context title must be attribute-escaped.' );
			native_check( false !== strpos( $html, "data-hssb-native-url='" . esc_attr( $url ) . "'" ), 'Native URL must remain raw before HTML escaping.' );
			native_check( false !== strpos( $html, '&quot;encoding&quot;:&quot;raw&quot;' ), 'Native browser descriptor must be raw.' );
			if ( in_array( 'copy', $ids, true ) ) {
				native_check( false !== strpos( $html, "<span class='native' hidden style='display:none'" ), 'Native must not duplicate the explicit no-JS Copy link.' );
			} else {
				native_check( false !== strpos( $html, "<a class='native' href='" . esc_attr( $url ) . "'" ), 'Native-only fallback must be a real link.' );
			}
		}
	}
}

$request = new RenderRequest( 'bootstrap-solid', 'square', RenderPlacement::SHORTCODE, '', array( 'pinterest' ) );
$html = $renderer->render( $request, $builder->build( $request, $context ) );
preg_match( "/href='([^']+)'/", $html, $matches );
parse_str( parse_url( html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' ), PHP_URL_QUERY ), $query );
native_check( $context->imageUrl() === $query['media'], 'Pinterest must preserve the complete image query after final-anchor escaping.' );
echo "Native share and Pinterest final-anchor contracts passed across every icon set and shape.\n";
