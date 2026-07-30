<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

final class ShareContext {
	private $permalink;
	private $title;
	private $description;
	private $imageUrl;

	public function __construct( $permalink, $title, $description = '', $imageUrl = '' ) {
		$this->permalink = (string) $permalink;
		$this->title = (string) $title;
		$this->description = (string) $description;
		$this->imageUrl = (string) $imageUrl;
	}

	public function permalink() {
		return $this->permalink;
	}

	public function title() {
		return $this->title;
	}

	public function description() {
		return $this->description;
	}

	public function imageUrl() {
		return $this->imageUrl;
	}
}
