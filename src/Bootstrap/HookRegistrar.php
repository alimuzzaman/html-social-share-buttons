<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

use InvalidArgumentException;

/**
 * Registers canonical WordPress subscribers once, after migrations finish.
 *
 * Subscribers are intentionally constructor-injected.  This keeps the
 * canonical kernel independent of global service locators and permits a
 * legacy bridge to be a consumer rather than a composition root.
 */
final class HookRegistrar {
	private $subscribers = array();
	private $registered = false;

	public function __construct( array $subscribers = array() ) {
		foreach ( $subscribers as $subscriber ) {
			$this->add( $subscriber );
		}
	}

	public function add( $subscriber ) {
		if ( $this->registered ) {
			throw new InvalidArgumentException(
				'Hook subscribers cannot be added after registration.'
			);
		}
		if ( ! is_object( $subscriber ) || ! method_exists( $subscriber, 'registerHooks' ) ) {
			throw new InvalidArgumentException(
				'Hook subscribers must expose a public registerHooks() method.'
			);
		}

		$this->subscribers[] = $subscriber;

		return $this;
	}

	public function registerHooks() {
		if ( $this->registered ) {
			return;
		}

		foreach ( $this->subscribers as $subscriber ) {
			$subscriber->registerHooks();
		}

		$this->registered = true;
	}

	public function isRegistered() {
		return $this->registered;
	}

	public function subscribers() {
		return $this->subscribers;
	}
}
