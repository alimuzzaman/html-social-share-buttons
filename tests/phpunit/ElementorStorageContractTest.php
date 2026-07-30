<?php

namespace Elementor {
	class Controls_Manager {
		const TAB_CONTENT = 'content';
		const TEXT = 'text';
		const SELECT = 'select';
		const SELECT2 = 'select2';
	}

	class Widget_Base {
		private $contractSettings = array();
		private $contractControls = array();

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
	}
}

namespace {
	final class ElementorStorageContractTest extends WP_UnitTestCase {
		private $contract;
		private $widget;

		protected function setUp(): void {
			parent::setUp();
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

			zm_sh_register_elementor_widget( $manager );
			$this->widget = $manager->widget;
		}

		public function testElementorWidgetIdentityAndStoredDocumentShapeRemainStable(): void {
			$this->assertInstanceOf( \ZM_SH_Elementor_Share_Widget::class, $this->widget );
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

			$this->assertSame( array( 'title', 'iconset', 'iconset_type', 'icons' ), array_keys( $fields ) );
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
		}

		public function testStoredElementorSettingsRenderThroughTheCompatibilityAdapter(): void {
			$output = $this->widget->contractRender( $this->contract['settings'] );

			$this->assertStringNotContainsString( '<h3>', $output );
			$this->assertStringContainsString( 'class="zmshbt in_elementor flat circle"', $output );
			$this->assertStringContainsString( 'class="facebook"', $output );
			$this->assertStringContainsString( 'class="twitter"', $output );
		}

		public function testEmptyElementorNetworkSelectionRendersNothing(): void {
			$settings = $this->contract['settings'];
			$settings['icons'] = array();

			$this->assertSame( '', $this->widget->contractRender( $settings ) );
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

			$this->assertStringContainsString( 'class="zmshbt in_elementor default square"', $output );
			$this->assertStringContainsString( 'class="facebook"', $output );
			$this->assertStringContainsString( 'class="twitter"', $output );
		}
	}
}
