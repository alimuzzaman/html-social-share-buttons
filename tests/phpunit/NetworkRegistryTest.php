<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\HtmlRenderer;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;

final class NetworkRegistryTest extends WP_UnitTestCase {
	public function testBuiltInNetworksHaveStableOrderAndTemplates(): void {
		$registry = ( new BuiltInNetworkProvider() )->createRegistry();

		$this->assertSame(
			array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'whatsapp', 'reddit', 'copy', 'mail' ),
			$registry->ids()
		);
		$this->assertSame( 'x', $registry->get( 'x' )->cssClass() );
		$this->assertSame( 'twitter', ( new HtmlRenderer() )->cssClass( $registry->get( 'x' ) ) );
		$this->assertFalse( $registry->get( 'telegram' )->enabledByDefault() );
		$this->assertSame(
			'https://bsky.app/intent/compose?text=%%title%%%0A%%permalink%%',
			$registry->get( 'bluesky' )->defaultShareTemplate()
		);
		foreach ( $registry->all() as $network ) {
			$this->assertNotSame( '', trim( $network->defaultShareTemplate() ) );
			$this->assertSame( 'copy' === $network->id(), $network->isCopyAction(), $network->id() );
		}
		foreach ( array( 'whatsapp', 'reddit', 'copy' ) as $id ) {
			$this->assertFalse( $registry->get( $id )->enabledByDefault(), $id );
		}
	}

	public function testUnknownNetworkKindsAreRejected(): void {
		$this->expectException( InvalidArgumentException::class );
		new Network( 'custom', 'Custom', 'custom', '%%permalink%%', array( '%%permalink%%' ), false, 'popup' );
	}

	public function testDuplicateNetworkIdsAreRejected(): void {
		$registry = ( new BuiltInNetworkProvider() )->createRegistry();

		$this->expectException( LogicException::class );
		$registry->register(
			new Network(
				'facebook',
				'Duplicate',
				'duplicate',
				'https://example.test/?url=%%permalink%%',
				array( '%%permalink%%' ),
				false
			)
		);
	}

	public function testUndeclaredTemplatePlaceholdersAreRejected(): void {
		$this->expectException( InvalidArgumentException::class );

		new Network(
			'example',
			'Example',
			'example',
			'https://example.test/?url=%%permalink%%&title=%%title%%',
			array( '%%permalink%%' ),
			false
		);
	}
}
