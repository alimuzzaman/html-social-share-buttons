<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet;

/**
 * Separates new-user choices from the complete runtime compatibility registry.
 */
final class IconSetSelectionPolicy {
	const NEW_DEFAULT_ID = 'bootstrap-solid';
	const LEGACY_DEFAULT_ID = 'default';

	private function __construct() {
	}

	/**
	 * The historical Default pack is selectable only while it is already selected.
	 */
	public static function choices( IconSetRegistry $registry, $selectedId = '' ) {
		$selectedId = (string) $selectedId;
		$choices = array();
		if ( $registry->has( self::NEW_DEFAULT_ID ) ) {
			$choices[] = $registry->get( self::NEW_DEFAULT_ID );
		}

		foreach ( $registry->all() as $iconSet ) {
			if ( self::NEW_DEFAULT_ID === $iconSet->id() ) {
				continue;
			}
			if ( self::LEGACY_DEFAULT_ID === $iconSet->id() && self::LEGACY_DEFAULT_ID !== $selectedId ) {
				continue;
			}
			$choices[] = $iconSet;
		}

		return $choices;
	}

	public static function isLegacy( $id ) {
		return self::LEGACY_DEFAULT_ID === (string) $id;
	}
}
