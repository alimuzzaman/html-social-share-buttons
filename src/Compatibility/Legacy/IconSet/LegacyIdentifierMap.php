<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet;

final class LegacyIdentifierMap {
	private $legacyToCanonical = array();
	private $canonicalToLegacy = array();

	public function canonicalIconSet( $legacyId ) {
		return $this->canonical( 'icon-set', $legacyId );
	}

	public function legacyIconSet( $canonicalId ) {
		return $this->legacy( 'icon-set', $canonicalId );
	}

	public function canonicalNetwork( $legacyId ) {
		return $this->canonical( 'network', $legacyId );
	}

	public function legacyNetwork( $canonicalId ) {
		return $this->legacy( 'network', $canonicalId );
	}

	public function canonicalShape( $canonicalIconSetId, $legacyId ) {
		return $this->canonical( 'shape:' . (string) $canonicalIconSetId, $legacyId );
	}

	public function legacyShape( $canonicalIconSetId, $canonicalId ) {
		return $this->legacy( 'shape:' . (string) $canonicalIconSetId, $canonicalId );
	}

	private function canonical( $scope, $legacyId ) {
		$legacyId = (string) $legacyId;
		if ( isset( $this->legacyToCanonical[ $scope ][ $legacyId ] ) ) {
			return $this->legacyToCanonical[ $scope ][ $legacyId ];
		}

		$canonicalId = preg_match( '/^[a-z][a-z0-9-]*$/', $legacyId )
			? $legacyId
			: 'legacy-' . bin2hex( $legacyId );
		$base = $canonicalId;
		$suffix = 2;
		while (
			isset( $this->canonicalToLegacy[ $scope ][ $canonicalId ] ) &&
			$this->canonicalToLegacy[ $scope ][ $canonicalId ] !== $legacyId
		) {
			$canonicalId = $base . '-' . $suffix;
			$suffix++;
		}

		$this->legacyToCanonical[ $scope ][ $legacyId ] = $canonicalId;
		$this->canonicalToLegacy[ $scope ][ $canonicalId ] = $legacyId;

		return $canonicalId;
	}

	private function legacy( $scope, $canonicalId ) {
		$canonicalId = (string) $canonicalId;

		return isset( $this->canonicalToLegacy[ $scope ][ $canonicalId ] )
			? $this->canonicalToLegacy[ $scope ][ $canonicalId ]
			: $canonicalId;
	}
}
