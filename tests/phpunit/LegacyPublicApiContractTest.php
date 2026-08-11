<?php

final class LegacyPublicApiContractTest extends WP_UnitTestCase {
	private $contract;

	protected function setUp(): void {
		parent::setUp();
		$this->contract = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/legacy-public-api-baseline.json' ),
			true
		);
	}

	public function testHistoricalConstantsFunctionsAndGlobalsRemainAvailable(): void {
		foreach ( $this->contract['constants'] as $constant ) {
			$this->assertTrue( defined( $constant ), 'Missing legacy constant ' . $constant . '.' );
		}

		foreach ( $this->contract['functions'] as $function ) {
			$this->assertTrue( function_exists( $function ), 'Missing legacy function ' . $function . '().' );
		}

		foreach ( $this->contract['globals'] as $name => $type ) {
			$this->assertTrue(
				array_key_exists( $name, $GLOBALS ),
				'Missing legacy global $' . $name . '.'
			);
			$value = $GLOBALS[ $name ];
			if ( 'array' === $type ) {
				$this->assertIsArray( $value, 'Legacy global $' . $name . ' changed type.' );
			} else {
				$this->assertInstanceOf( $type, $value, 'Legacy global $' . $name . ' changed type.' );
			}
		}
	}

	public function testHistoricalClassesRetainTheirPublicSurface(): void {
		foreach ( $this->contract['classes'] as $class => $surface ) {
			$this->assertTrue( class_exists( $class ), 'Missing legacy class ' . $class . '.' );
			$reflection = new ReflectionClass( $class );

			foreach ( $surface['properties'] as $property ) {
				$this->assertTrue(
					$reflection->hasProperty( $property ),
					'Missing legacy property ' . $class . '::$' . $property . '.'
				);
				$this->assertTrue(
					$reflection->getProperty( $property )->isPublic(),
					'Legacy property ' . $class . '::$' . $property . ' is no longer public.'
				);
			}

			foreach ( $surface['methods'] as $method ) {
				$this->assertTrue(
					$reflection->hasMethod( $method ),
					'Missing legacy method ' . $class . '::' . $method . '().'
				);
				$this->assertTrue(
					$reflection->getMethod( $method )->isPublic(),
					'Legacy method ' . $class . '::' . $method . '() is no longer public.'
				);
			}
		}
	}

	public function testHistoricalIconsetInterfaceRemainsAvailable(): void {
		foreach ( $this->contract['interfaces'] as $interface => $methods ) {
			$this->assertTrue( interface_exists( $interface ), 'Missing legacy interface ' . $interface . '.' );
			$reflection = new ReflectionClass( $interface );
			foreach ( $methods as $method ) {
				$this->assertTrue(
					$reflection->hasMethod( $method ),
					'Missing legacy interface method ' . $interface . '::' . $method . '().'
				);
			}
		}
	}

	public function testHistoricalFunctionAndMethodSignaturesRemainCompatible(): void {
		foreach ( $this->contract['signatures']['functions'] as $function => $signature ) {
			$this->assertSame(
				$signature,
				$this->signature( new ReflectionFunction( $function ) ),
				'Legacy function signature changed for ' . $function . '().'
			);
		}

		foreach ( $this->contract['signatures']['methods'] as $callable => $signature ) {
			list( $class, $method ) = explode( '::', $callable, 2 );
			$this->assertSame(
				$signature,
				$this->signature( new ReflectionMethod( $class, $method ) ),
				'Legacy method signature changed for ' . $callable . '().'
			);
		}
	}

	public function testHistoricalIconsetMagicPropertiesAndBuiltInsRemainCompatible(): void {
		global $zm_sh;

		$registry = $zm_sh->iconsets;
		$current = $registry->get_current_iconset();

		$this->assertSame( $current, $registry->curr_iconset );
		$this->assertSame( $current, $registry->private );

		foreach ( $this->contract['built_in_iconsets'] as $id ) {
			$iconset = $registry->get_iconset( $id );
			$this->assertInstanceOf( '__iconset_parent_class', $iconset );
			$this->assertSame( $id, $iconset->id );
			$this->assertNotEmpty( $iconset->name );
			$this->assertIsArray( $iconset->types );
			$this->assertIsArray( $iconset->icons );
			$this->assertNotEmpty( $iconset->dir );
			$this->assertNotEmpty( $iconset->url );
			$this->assertNotEmpty( $iconset->stylesheet_url );
			$this->assertNotEmpty( $iconset->preview_img_url );
			$this->assertFileExists( $iconset->preview_img_dir );
		}
	}

	private function signature( ReflectionFunctionAbstract $reflection ): string {
		$parameters = array();
		foreach ( $reflection->getParameters() as $parameter ) {
			$value =
				( $parameter->isPassedByReference() ? '&' : '' ) .
				( $parameter->isVariadic() ? '...' : '' ) .
				$parameter->getName();
			if ( $parameter->isDefaultValueAvailable() ) {
				$value .= '=' . wp_json_encode( $parameter->getDefaultValue() );
			}
			$parameters[] = $value;
		}

		return implode( ',', $parameters );
	}
}
