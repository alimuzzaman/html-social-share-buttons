<?php
namespace Mediavine\Grow;

use Exception;
use Throwable;

/**
 * Exception representing a general HTTP 4xx error.
 */
class HTTP_Client_Exception extends Exception {

	/**
	 * Configure the exception.
	 *
	 * @param string         $message  The Exception message to throw.
	 * @param int            $code     The Exception code.
	 * @param Throwable|null $previous The previous throwable used for the exception chaining.
	 */
	public function __construct( $message = '', $code = 400, Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}
