<?php

namespace Mediavine\Grow;

use JsonSerializable;
/**
 * Simple data class for configuring a share count URL.
 */
class Share_Count_URL implements JsonSerializable {

	/** @var bool */
	private $recently_published;

	/** @var string */
	private $url;

	/**
	 * Set up the share URL.
	 *
	 * @param string $url URL for determining counts.
	 * @param bool   $recently_published Was the URL published within the last seven days?
	 */
	public function __construct( string $url, bool $recently_published ) {
		$this->url                = $url;
		$this->recently_published = $recently_published;
	}

	/**
	 * Specify data which should be serialized to JSON.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'recently_published' => $this->recently_published,
			'url'                => $this->url,
		];
	}
}
