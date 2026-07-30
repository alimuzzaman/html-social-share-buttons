<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;

final class BuiltInNetworkProvider {
	public function createRegistry() {
		$registry = new NetworkRegistry();

		foreach ( $this->definitions() as $definition ) {
			$registry->register(
				new Network(
					$definition['id'],
					$definition['label'],
					$definition['css_class'],
					$definition['template'],
					$definition['placeholders'],
					$definition['enabled_by_default']
				)
			);
		}

		return $registry;
	}

	private function definitions() {
		return array(
			array(
				'id'                 => 'facebook',
				'label'              => 'Facebook',
				'css_class'          => 'facebook',
				'template'           => 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
				'placeholders'       => array( '%%permalink%%' ),
				'enabled_by_default' => true,
			),
			array(
				'id'                 => 'x',
				'label'              => 'X',
				'css_class'          => 'x',
				'template'           => 'https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%',
				'placeholders'       => array( '%%permalink%%', '%%title%%' ),
				'enabled_by_default' => true,
			),
			array(
				'id'                 => 'linkedin',
				'label'              => 'LinkedIn',
				'css_class'          => 'linkedin',
				'template'           => 'https://www.linkedin.com/sharing/share-offsite/?url=%%permalink%%',
				'placeholders'       => array( '%%permalink%%' ),
				'enabled_by_default' => true,
			),
			array(
				'id'                 => 'pinterest',
				'label'              => 'Pinterest',
				'css_class'          => 'pinterest',
				'template'           => 'https://www.pinterest.com/pin/create/button/?url=%%permalink%%&media=%%imageurl%%&description=%%title%%',
				'placeholders'       => array( '%%permalink%%', '%%imageurl%%', '%%title%%' ),
				'enabled_by_default' => true,
			),
			array(
				'id'                 => 'telegram',
				'label'              => 'Telegram',
				'css_class'          => 'telegram',
				'template'           => 'https://t.me/share/url?url=%%permalink%%&text=%%title%%',
				'placeholders'       => array( '%%permalink%%', '%%title%%' ),
				'enabled_by_default' => false,
			),
			array(
				'id'                 => 'bluesky',
				'label'              => 'Bluesky',
				'css_class'          => 'bluesky',
				'template'           => 'https://bsky.app/intent/compose?text=%%title%%%0A%%permalink%%',
				'placeholders'       => array( '%%title%%', '%%permalink%%' ),
				'enabled_by_default' => false,
			),
			array(
				'id'                 => 'mail',
				'label'              => 'Email',
				'css_class'          => 'mail',
				'template'           => 'mailto:?subject=%%title%%&body=%%permalink%%',
				'placeholders'       => array( '%%title%%', '%%permalink%%' ),
				'enabled_by_default' => true,
			),
		);
	}
}
