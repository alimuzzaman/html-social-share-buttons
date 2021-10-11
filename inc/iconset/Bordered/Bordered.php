<?php

namespace HSSB\Iconset;

use HSSB\Icon;
use HSSB\Iconset;

class Bordered extends Iconset
{
	public $id			= 'default';
	public $name		= 'Default';
	public $types		= array("square");

	public function __construct()
	{
		$this->push_icon(new Icon(array(
			'id' => 'facebook',
			'name' => "Facebook",
			'class' => 'facebook',
			'image' => 'facebook.png',
			'url' => "http://www.facebook.com/sharer.php?u=%%permalink%%&amp;t=%%title%%",
		)));
		$this->push_icon(new Icon(array(
			'id' => 'twitter',
			'name' => "Twitter",
			'class' => 'twitter',
			'image' => 'twitter.png',
			'url' => "http://twitter.com/share?url=%%permalink%%&amp;text=%%title%%",
		)));
		$this->push_icon(new Icon(array(
			'id' => 'linkedin',
			'name' => "Linkedin",
			'class' => 'linkedin',
			'image' => 'linkedin.png',
			'url' => "http://www.linkedin.com/shareArticle?mini=true&url=%%permalink%%&amp;title=%%title%%",
		)));
		$this->push_icon(new Icon(array(
			'id' => 'googlepluse',
			'name' => "Google Plus",
			'class' => 'googlepluse',
			'image' => 'googlepluse.png',
			'url' => "https://plus.google.com/share?url=%%permalink%%",
		)));
		$this->push_icon(new Icon(array(
			'id' => 'bookmark',
			'name' => "Google Bookmarks",
			'class' => 'bookmark',
			'image' => 'bookmark.png',
			'url' => "http://www.google.com/bookmarks/mark?op=edit&bkmk=%%permalink%%&amp;title=%%title%%&annotation=%%description%%",
		)));
		$this->push_icon(new Icon(array(
			'id' => 'pinterest',
			'name' => "Pinterest",
			'class' => 'pinterest',
			'image' => 'pinterest.png',
			'url' => "http://pinterest.com/pin/create/button/?url=%%permalink%%&amp;media=%%imageurl%%&amp;description=%%title%%",
		)));
		$this->push_icon(new Icon(array(
			'id' => 'mail',
			'name' => "Email",
			'class' => 'mail',
			'image' => 'mail.png',
			'url' => "mailto:?subject=I wanted you to see this site&amp;body=This is about %%title%% %%permalink%%",
		)));
	}
}
