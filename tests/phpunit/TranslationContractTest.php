<?php

final class TranslationContractTest extends WP_UnitTestCase {
	public function testCanonicalAndLegacyFrenchCatalogsRemainLoadable(): void {
		$root = dirname( __DIR__, 2 );
		$canonicalDomain = 'html-social-share-buttons';
		$legacyDomain = 'zm-sh';

		unload_textdomain( $canonicalDomain );
		unload_textdomain( $legacyDomain );

		$this->assertTrue(
			load_textdomain(
				$canonicalDomain,
				$root . '/languages/html-social-share-buttons-fr_FR.mo'
			)
		);
		$this->assertTrue(
			load_textdomain(
				$legacyDomain,
				$root . '/languages/zm-sh-fr_FR.mo'
			)
		);
		$this->assertSame( 'Gauche', translate( 'Left', $canonicalDomain ) );
		$this->assertSame( 'Gauche', translate( 'Left', $legacyDomain ) );
	}

	public function testLegacyCatalogFallbackPreservesExistingSiteTranslations(): void {
		$root = dirname( __DIR__, 2 );
		$legacyDomain = 'zm-sh';

		unload_textdomain( $legacyDomain );
		$this->assertTrue(
			load_textdomain(
				$legacyDomain,
				$root . '/languages/zm-sh-fr_FR.mo'
			)
		);

		global $zm_sh;
		$this->assertSame(
			'Gauche',
			$zm_sh->translate_legacy_domain( 'Left', 'Left', 'html-social-share-buttons' )
		);
		$this->assertSame(
			'Already translated',
			$zm_sh->translate_legacy_domain(
				'Already translated',
				'Left',
				'html-social-share-buttons'
			)
		);
		$this->assertSame(
			'Left',
			$zm_sh->translate_legacy_domain( 'Left', 'Left', 'another-domain' )
		);
	}
}
