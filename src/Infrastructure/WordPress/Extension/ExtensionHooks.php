<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;

final class ExtensionHooks {
	const NETWORKS = 'hssb/networks';
	const ICON_SETS = 'hssb/icon_sets';
	const SHARE_TEMPLATES = 'hssb/share_templates';
	const SHARE_TEMPLATE = 'hssb/share_template';
	const SHARE_TITLE = 'hssb/share_title';
	const SHARE_URL = 'hssb/share_url';
	const SETTINGS_SCHEMA = 'hssb/settings_schema';

	public function networks( NetworkRegistry $registry ) {
		$filtered = apply_filters( self::NETWORKS, $registry );

		return $filtered instanceof NetworkRegistry ? $filtered : $registry;
	}

	public function iconSets( IconSetRegistry $registry ) {
		$filtered = apply_filters( self::ICON_SETS, $registry );

		return $filtered instanceof IconSetRegistry ? $filtered : $registry;
	}

	public function shareTemplates( array $templates ) {
		$filtered = apply_filters( self::SHARE_TEMPLATES, $templates );

		return is_array( $filtered ) ? $filtered : $templates;
	}

	public function shareTemplate( $template, $networkId, $fallback ) {
		return apply_filters(
			self::SHARE_TEMPLATE,
			$template,
			(string) $networkId,
			$fallback
		);
	}

	public function shareTitle( $title ) {
		return apply_filters( self::SHARE_TITLE, $title );
	}

	public function shareUrl( $url ) {
		return apply_filters( self::SHARE_URL, $url );
	}

	public function settingsSchema( SettingsSchema $schema ) {
		$filtered = apply_filters( self::SETTINGS_SCHEMA, $schema );

		return $filtered instanceof SettingsSchema ? $filtered : $schema;
	}
}
