<?php

final class HookRegistrationContractTest extends WP_UnitTestCase {
	private $surface;

	protected function setUp(): void {
		parent::setUp();
		$this->surface = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/wordpress-surface-baseline.json' ),
			true
		);

		global $zm_sh;
		$zm_sh->plugins_loaded();
		new zm_sh_settings();
		new zm_sh_metabox();
	}

	public function testHookPrioritiesCallbacksAndAcceptedArgumentsMatchTheContract(): void {
		foreach ( $this->surface['hooks'] as $contract ) {
			$registration = $this->findRegistration( $contract );

			$this->assertNotNull(
				$registration,
				sprintf(
					'Missing callback contract for %s at priority %d.',
					$contract['hook'],
					$contract['priority']
				)
			);
			$this->assertSame(
				$contract['accepted_args'],
				$registration['accepted_args'],
				'Accepted argument count changed for ' . $contract['hook'] . '.'
			);
		}
	}

	private function findRegistration( array $contract ) {
		global $wp_filter;
		$hook = 'plugin_action_links_{plugin_basename}' === $contract['hook']
			? 'plugin_action_links_' . plugin_basename(
				dirname( __DIR__, 2 ) . '/html-social-share.php'
			)
			: $contract['hook'];

		if (
			! isset( $wp_filter[ $hook ] ) ||
			! isset( $wp_filter[ $hook ]->callbacks[ $contract['priority'] ] )
		) {
			return null;
		}

		foreach ( $wp_filter[ $hook ]->callbacks[ $contract['priority'] ] as $registration ) {
			$callback = $registration['function'];
			if ( isset( $contract['callback'] ) && $callback === $contract['callback'] ) {
				return $registration;
			}
			if (
				isset( $contract['class'], $contract['method'] ) &&
				is_array( $callback ) &&
				isset( $callback[0], $callback[1] ) &&
				is_object( $callback[0] ) &&
				$callback[0] instanceof $contract['class'] &&
				$contract['method'] === $callback[1]
			) {
				return $registration;
			}
			if (
				isset( $contract['closure_file'] ) &&
				$callback instanceof Closure
			) {
				$reflection = new ReflectionFunction( $callback );
				$file = str_replace( '\\', '/', (string) $reflection->getFileName() );
				if ( substr( $file, -strlen( $contract['closure_file'] ) ) === $contract['closure_file'] ) {
					return $registration;
				}
			}
		}

		return null;
	}

	public function testConditionalContentHookRetainsItsHistoricalRegistration(): void {
		global $wp_filter;
		update_option(
			'zm_shbt_fld',
			array(
				'show_after_post' => 'square',
				'show_in' => array( 'show_after_post' => '1' ),
			)
		);
		$renderer = new zm_social_share();

		$registration = null;
		foreach ( $wp_filter['the_content']->callbacks[10] as $candidate ) {
			$callback = $candidate['function'];
			if (
				is_array( $callback ) &&
				isset( $callback[0], $callback[1] ) &&
				$callback[0] === $renderer &&
				'filter_the_content' === $callback[1]
			) {
				$registration = $candidate;
				break;
			}
		}

		$this->assertNotNull( $registration );
		$this->assertSame( 1, $registration['accepted_args'] );
		remove_filter( 'the_content', array( $renderer, 'filter_the_content' ), 10 );
	}

	public function testAdminOnlyMetaboxLoadHooksRemainInTheCompatibilitySource(): void {
		$source = (string) file_get_contents(
			dirname( __DIR__, 2 ) . '/src/Compatibility/Legacy/Global/metabox.php'
		);

		$this->assertStringContainsString(
			"add_action( 'load-post.php', 'zm_sh_metabox_new' );",
			$source
		);
		$this->assertStringContainsString(
			"add_action( 'load-post-new.php', 'zm_sh_metabox_new' );",
			$source
		);
	}
}
