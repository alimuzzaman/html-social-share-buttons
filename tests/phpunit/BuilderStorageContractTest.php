<?php

final class BuilderStorageContractTest extends WP_UnitTestCase {
	private $storage;

	protected function setUp(): void {
		parent::setUp();
		$this->storage = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/builder-storage-baseline.json' ),
			true
		);
	}

	public function testDynamicBlockSerializationAndParsingRemainStable(): void {
		$contract = $this->storage['block'];
		$block = array(
			'blockName' => $contract['name'],
			'attrs' => $contract['attributes'],
			'innerBlocks' => array(),
			'innerHTML' => '',
			'innerContent' => array(),
		);

		$this->assertSame( $contract['serialized'], serialize_block( $block ) );

		$parsed = parse_blocks( $contract['serialized'] );
		$this->assertCount( 1, $parsed );
		$this->assertSame( $contract['name'], $parsed[0]['blockName'] );
		$this->assertSame( $contract['attributes'], $parsed[0]['attrs'] );
	}

	public function testStoredBlockRendersThroughTheExistingCompatibilityAdapter(): void {
		$output = do_blocks( $this->storage['block']['serialized'] );

		$this->assertStringContainsString( "class='zmshbt in_block default square'", $output );
		$this->assertStringContainsString( "class='facebook'", $output );
		$this->assertStringContainsString( "class='twitter'", $output );
		$this->assertStringNotContainsString( '<h3>', $output );
	}

	public function testWpbakeryShortcodeStorageAndRenderingRemainStable(): void {
		$contract = $this->storage['wpbakery'];
		$matches = array();
		$this->assertSame( 1, preg_match( '/^\\[zm_sh_btn\\s+(.+)\\]$/', $contract['shortcode'], $matches ) );
		$attributes = shortcode_parse_atts( $matches[1] );

		$this->assertSame( $contract['attributes'], $attributes );

		$output = do_shortcode( $contract['shortcode'] );
		$this->assertStringStartsWith( '<h3>Stored title</h3>', $output );
		$this->assertStringContainsString( "class='zmshbt in_shortcode flat circle'", $output );
		$this->assertStringContainsString( "class='twitter'", $output );
	}

	public function testDefaultStoredBlockNeverEmitsTheHistoricalPermalinkPlaceholderDefect(): void {
		$output = do_blocks( $this->storage['block']['serialized'] );

		$this->assertStringNotContainsString( '%%permalink%%', $output );
		$this->assertStringNotContainsString( '%25%25permalink%25%25', $output );
		$this->assertStringNotContainsString( 'http%3A%2F%2F%25%25permalink%25%25', $output );
	}

	public function testBlockRenderingDoesNotDependOnTheRegisteredShortcodeCallback(): void {
		global $shortcode_tags;

		$callback = isset( $shortcode_tags['zm_sh_btn'] ) ? $shortcode_tags['zm_sh_btn'] : null;
		remove_shortcode( 'zm_sh_btn' );
		try {
			$output = do_blocks( $this->storage['block']['serialized'] );
		} finally {
			if ( null !== $callback ) {
				$shortcode_tags['zm_sh_btn'] = $callback;
			}
		}

		$this->assertStringContainsString( "class='zmshbt in_block default square'", $output );
		$this->assertStringContainsString( "class='facebook'", $output );
	}

	public function testMalformedBlockAndShortcodeAttributesFailClosedWithoutTypeErrors(): void {
		$blockOutput = zm_sh_render_block(
			array(
				'title' => array( 'invalid' ),
				'iconset' => new stdClass(),
				'iconset_type' => array( 'invalid' ),
				'icons' => array( array( 'invalid' ), 'FACEBOOK', 'x', null ),
			)
		);

		$this->assertStringContainsString( "class='zmshbt in_block default square'", $blockOutput );
		$this->assertStringContainsString( "class='facebook'", $blockOutput );
		$this->assertStringContainsString( "class='twitter'", $blockOutput );

		$shortcodeOutput = zm_sh_shortcode_cb(
			array(
				'title' => new stdClass(),
				'iconset' => array( 'invalid' ),
				'url' => array( 'invalid' ),
				'iconset_type' => new stdClass(),
				'class' => array( 'invalid' ),
				'icons' => array( array( 'invalid' ), 'facebook' ),
			)
		);

		$this->assertStringContainsString( "class='zmshbt in_shortcode default square'", $shortcodeOutput );
		$this->assertStringContainsString( "class='facebook'", $shortcodeOutput );
	}

	public function testNonArrayBlockAttributesUseTheHistoricalDefaults(): void {
		$output = zm_sh_render_block( 'invalid-storage' );

		$this->assertStringContainsString( "class='zmshbt in_block default square'", $output );
		$this->assertStringContainsString( "class='facebook'", $output );
		$this->assertStringContainsString( "class='twitter'", $output );
		$this->assertStringContainsString( "class='linkedin'", $output );
		$this->assertStringContainsString( "class='pinterest'", $output );
		$this->assertStringContainsString( "class='mail'", $output );
	}
}
