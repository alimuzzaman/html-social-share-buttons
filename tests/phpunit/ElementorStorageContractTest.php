<?php

namespace Elementor {
	class Plugin {
		public static $instance;
	}

	class Controls_Manager {
		const TAB_CONTENT = 'content';
		const TEXT = 'text';
		const SELECT = 'select';
		const SELECT2 = 'select2';
	}

	class Widget_Base {
		private $contractSettings = array();
		private $contractControls = array();

		public function __construct( array $data = array(), array $args = array() ) {
			$this->contractSettings = isset( $data['settings'] ) && is_array( $data['settings'] )
				? $data['settings']
				: array();
		}

		protected function start_controls_section( $id, array $definition ) {
			$this->contractControls[ $id ] = array(
				'definition' => $definition,
				'controls'   => array(),
			);
		}

		protected function add_control( $id, array $definition ) {
			$sections = array_keys( $this->contractControls );
			$section = end( $sections );
			$this->contractControls[ $section ]['controls'][ $id ] = $definition;
		}

		protected function end_controls_section() {
		}

		protected function get_settings_for_display() {
			return $this->contractSettings;
		}

		public function contractControls() {
			$this->register_controls();

			return $this->contractControls;
		}

		public function contractRender( array $settings ) {
			$this->contractSettings = $settings;
			ob_start();
			$this->render();

			return (string) ob_get_clean();
		}

		public function contractRenderStoredData() {
			ob_start();
			$this->render();

			return (string) ob_get_clean();
		}
	}
}

namespace {
	final class ElementorStorageContractTest extends WP_UnitTestCase {
		private $contract;
		private $widget;
		private $originalOptions;

		protected function setUp(): void {
			parent::setUp();
			$this->originalOptions = get_option( 'zm_shbt_fld', null );
			$storage = json_decode(
				(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/builder-storage-baseline.json' ),
				true
			);
			$this->contract = $storage['elementor'];
			$manager = new class() {
				public $widget;

				public function register( $widget ) {
					$this->widget = $widget;
				}
			};

			\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()
				->elementor()
				->registerWidget( $manager );
			$this->widget = $manager->widget;
		}

		protected function tearDown(): void {
			\Elementor\Plugin::$instance = null;
			if ( null === $this->originalOptions ) {
				delete_option( 'zm_shbt_fld' );
			} else {
				update_option( 'zm_shbt_fld', $this->originalOptions );
			}
			parent::tearDown();
		}

		public function testElementorWidgetIdentityAndStoredDocumentShapeRemainStable(): void {
			$this->assertInstanceOf(
				\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Elementor\ElementorShareWidget::class,
				$this->widget
			);
			$this->assertSame( $this->contract['widget_name'], $this->widget->get_name() );
			$this->assertSame( 'eicon-share', $this->widget->get_icon() );
			$this->assertSame( array( 'general' ), $this->widget->get_categories() );
			$this->assertSame(
				$this->contract['settings'],
				$this->contract['document_element']['settings']
			);
			$this->assertSame(
				$this->contract['widget_name'],
				$this->contract['document_element']['widgetType']
			);
		}

		public function testElementorControlsPreserveNamesTypesAndDefaults(): void {
			$controls = $this->widget->contractControls();
			$fields = $controls['content_section']['controls'];

		$this->assertSame( array( 'title', 'iconset', 'iconset_type', 'icons', 'profile_links_mode' ), array_keys( $fields ) );
			$this->assertSame( \Elementor\Controls_Manager::TEXT, $fields['title']['type'] );
			$this->assertSame( \Elementor\Controls_Manager::SELECT, $fields['iconset']['type'] );
			$this->assertSame( 'inherit', $fields['iconset']['default'] );
			$this->assertSame( \Elementor\Controls_Manager::SELECT, $fields['iconset_type']['type'] );
			$this->assertSame( 'square', $fields['iconset_type']['default'] );
			$this->assertSame( \Elementor\Controls_Manager::SELECT2, $fields['icons']['type'] );
			$this->assertTrue( $fields['icons']['multiple'] );
		$this->assertSame(
			array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' ),
			$fields['icons']['default']
		);
		$this->assertSame( \Elementor\Controls_Manager::SELECT, $fields['profile_links_mode']['type'] );
		$this->assertSame( 'inherit', $fields['profile_links_mode']['default'] );
		}

		public function testStoredElementorSettingsRenderThroughTheCanonicalWidget(): void {
			$output = $this->widget->contractRender( $this->contract['settings'] );

			$this->assertStringNotContainsString( '<h3>', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]zmshbt in_elementor flat circle[\'"]/', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]facebook[\'"]/', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]twitter[\'"]/', $output );
		}

		public function testElementorStyleRehydrationUsesTheConfiguredCanonicalDependencies(): void {
			$class = get_class( $this->widget );
			$rehydrated = new $class( $this->contract['document_element'], array() );
			$output = $rehydrated->contractRenderStoredData();

			$this->assertSame( $this->contract['widget_name'], $rehydrated->get_name() );
			$this->assertMatchesRegularExpression( '/class=[\'"]zmshbt in_elementor flat circle[\'"]/', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]facebook[\'"]/', $output );
		}

		public function testElementorDeclaresRegisteredIconPackStylesWithoutRendering(): void {
			$handles = $this->widget->get_style_depends();

			$this->assertContains( 'social-share-default', $handles );
			$this->assertContains( 'social-share-flat', $handles );
			$this->assertTrue( wp_style_is( 'social-share-default', 'registered' ) );
			$this->assertTrue( wp_style_is( 'social-share-flat', 'registered' ) );
		}

		public function testElementorEditorPreviewIncludesTheRenderedIconRules(): void {
			\Elementor\Plugin::$instance = (object) array(
				'editor' => new class() {
					public function is_edit_mode() {
						return true;
					}
				},
			);

			$output = $this->widget->contractRender( $this->contract['settings'] );

			$this->assertStringContainsString( '<style>', $output );
			$this->assertStringContainsString( '.zmshbt.flat.circle .facebook', $output );
			$this->assertStringContainsString( 'Facebook.png', $output );
		}

		public function testEmptyElementorNetworkSelectionRendersNothing(): void {
			$settings = $this->contract['settings'];
			$settings['icons'] = array();

			$this->assertSame( '', $this->widget->contractRender( $settings ) );
		}

	public function testStoredElementorSettingsInheritGlobalProfileLinks(): void {
			update_option(
				'zm_shbt_fld',
				array(
					'profile_links' => array(
						'facebook' => 'https://www.facebook.com/hssb',
						'mail'     => 'mailto:hello@example.com',
					),
				)
			);

			$output = $this->widget->contractRender( $this->contract['settings'] );

			$this->assertMatchesRegularExpression( '/class=[\'"]facebook zmshbt-profile-link[\'"]/', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]mail zmshbt-profile-link[\'"]/', $output );
			$this->assertStringContainsString( 'https://www.facebook.com/hssb', $output );
		$this->assertStringContainsString( 'mailto:hello@example.com', $output );
	}

	public function testElementorCanHideInheritedProfileLinksWithTheAdditiveMode(): void {
		update_option(
			'zm_shbt_fld',
			array( 'profile_links' => array( 'facebook' => 'https://www.facebook.com/hssb' ) )
		);
		$settings = $this->contract['settings'];
		$settings['profile_links_mode'] = 'none';

		$output = $this->widget->contractRender( $settings );

		$this->assertStringNotContainsString( 'zmshbt-profile-link', $output );
	}

		public function testMalformedElementorSettingsFailClosedWithoutTypeErrors(): void {
			$output = $this->widget->contractRender(
				array(
					'title' => array( 'invalid' ),
					'iconset' => new \stdClass(),
					'iconset_type' => array( 'invalid' ),
					'icons' => array( array( 'invalid' ), 'FACEBOOK', 'x', null ),
				)
			);

			$this->assertMatchesRegularExpression( '/class=[\'"]zmshbt in_elementor default square[\'"]/', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]facebook[\'"]/', $output );
			$this->assertMatchesRegularExpression( '/class=[\'"]twitter[\'"]/', $output );
		}
	}
}
