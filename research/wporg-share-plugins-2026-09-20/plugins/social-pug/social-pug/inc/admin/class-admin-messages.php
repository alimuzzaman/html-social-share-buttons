<?php

namespace Mediavine\Grow;

use InvalidArgumentException;

/**
 * Handles storing and retrieving a set of messages for the admin
 */
class Admin_Messages {

	/** @var string MESSAGE_TYPE_ERROR Key for Error message type */
	public const MESSAGE_TYPE_ERROR = 'error';

	/** @var string MESSAGE_TYPE_INFO Key for Info message type */
	public const MESSAGE_TYPE_INFO = 'info';

	/** @var string MESSAGE_TYPE_WARNING Key for Warning message type */
	public const MESSAGE_TYPE_WARNING = 'warning';

	/** @var string MESSAGE_TYPE_SUCCESS Key for Success message type */
	public const MESSAGE_TYPE_SUCCESS = 'success';

	/** @var array<string, array{message: string, type: string}> $messages Stores the messages */
	private $messages = [
		self::MESSAGE_TYPE_ERROR   => [],
		self::MESSAGE_TYPE_INFO    => [],
		self::MESSAGE_TYPE_WARNING => [],
		self::MESSAGE_TYPE_SUCCESS => [],
	];

	/** @var array<string, string> Valid message types. Initial value is generated on-demand. */
	private $valid_types;

	/**
	 * Returns the string representation of the object.
	 *
	 * @return string
	 */
	public function __toString() {
		if ( $this->is_empty() ) {
			return '';
		}

		$result = '<div class="wrap">';
		foreach ( $this->messages as $message_type_messages ) {
			foreach ( $message_type_messages as $message ) {
				$result .= '<div class="dpsp-admin-notice inline notice ' . esc_attr( 'notice-' . $message['type'] ) . '">';
				$result .= '<p>' . $message['message'] . '</p>';
				$result .= '</div>';
			}
		}
		$result .= '</div>';

		return $result;
	}

	/**
	 * Add a new message to the collection.
	 *
	 * @param string $message Message body to include.
	 * @param string $type Type of message. Must be one of the MESSAGE_TYPE_* constants.
	 * @throws InvalidArgumentException If the message type is not one of the MESSAGE_TYPE_* constants.
	 */
	public function add_message( string $message, string $type = self::MESSAGE_TYPE_INFO ) {
		if ( ! $this->is_valid_type( $type ) ) {
			throw new InvalidArgumentException( "Invalid message type: {$type}." );
		}
		$this->messages[ $type ][] = [
			'message' => $message,
			'type'    => $type,
		];
	}

	/**
	 * Get all messages.
	 *
	 * @return array<string, array{message: string, type: int}>
	 */
	public function get_messages() : array {
		return $this->messages;
	}

	/**
	 * Get all known valid message types.
	 *
	 * @return string[]
	 */
	private function get_valid_types() : array {
		if ( null === $this->valid_types ) {
			$types             = array_keys( $this->messages );
			$this->valid_types = array_combine( $types, $types );
		}
		return $this->valid_types;
	}

	/**
	 * Should this object be considered empty?
	 *
	 * @return bool
	 */
	private function is_empty() : bool {
		$result = true;
		foreach ( $this->messages as $type_messages ) {
			if ( count( $type_messages ) > 0 ) {
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Is the type a valid message type?
	 *
	 * @param string $type Message type to validate.
	 * @return bool
	 */
	private function is_valid_type( string $type ) : bool {
		return array_key_exists( $type, $this->get_valid_types() );
	}
}
