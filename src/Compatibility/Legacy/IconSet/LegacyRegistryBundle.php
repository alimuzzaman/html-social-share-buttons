<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;

final class LegacyRegistryBundle {
	private $networks;
	private $iconSets;
	private $identifiers;

	public function __construct(
		NetworkRegistry $networks,
		IconSetRegistry $iconSets,
		LegacyIdentifierMap $identifiers
	) {
		$this->networks = $networks;
		$this->iconSets = $iconSets;
		$this->identifiers = $identifiers;
	}

	public function networks() {
		return $this->networks;
	}

	public function iconSets() {
		return $this->iconSets;
	}

	public function canonicalIconSetId( $legacyId ) {
		return $this->identifiers->canonicalIconSet( $legacyId );
	}

	public function legacyIconSetId( $canonicalId ) {
		return $this->identifiers->legacyIconSet( $canonicalId );
	}

	public function canonicalNetworkId( $legacyId ) {
		return $this->identifiers->canonicalNetwork( $legacyId );
	}

	public function legacyNetworkId( $canonicalId ) {
		return $this->identifiers->legacyNetwork( $canonicalId );
	}

	public function canonicalShapeId( $canonicalIconSetId, $legacyId ) {
		return $this->identifiers->canonicalShape( $canonicalIconSetId, $legacyId );
	}

	public function legacyShapeId( $canonicalIconSetId, $canonicalId ) {
		return $this->identifiers->legacyShape( $canonicalIconSetId, $canonicalId );
	}
}
