<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use RuntimeException;

final class ManifestIconSetProvider {
	private $manifestDirectory;

	public function __construct( $manifestDirectory ) {
		$this->manifestDirectory = rtrim( (string) $manifestDirectory, '/\\' );
	}

	public function createRegistry( NetworkRegistry $networks ) {
		$registry = new IconSetRegistry( $networks );

		foreach (
			array(
				'default',
				'flat',
				'long-shadows',
				'prajin',
				'bootstrap-solid',
				'tabler-outline',
			) as $id
		) {
			$definition = $this->loadDefinition( $id );
			$registry->register(
				new IconSet(
					$definition['id'],
					$definition['label'],
					$definition['stylesheet'],
					$definition['preview'],
					$definition['shapes'],
					$definition['icons']
				)
			);
		}

		return $registry;
	}

	private function loadDefinition( $id ) {
		$file = $this->manifestDirectory . DIRECTORY_SEPARATOR . $id . '.php';
		if ( ! is_file( $file ) ) {
			throw new RuntimeException( 'A built-in icon-set manifest is missing.' );
		}

		$definition = require $file;
		$requiredKeys = array( 'id', 'label', 'stylesheet', 'preview', 'shapes', 'icons' );
		if ( ! is_array( $definition ) || array_diff( $requiredKeys, array_keys( $definition ) ) ) {
			throw new RuntimeException( 'A built-in icon-set manifest is invalid.' );
		}
		if ( $id !== $definition['id'] ) {
			throw new RuntimeException( 'The icon-set manifest ID does not match its file name.' );
		}

		return $definition;
	}
}
