<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

/**
 * Preserves the PHP truthiness semantics of the established option values.
 *
 * Existing installations can contain values such as the non-empty string
 * "false". Changing their meaning during a rewrite would alter live output.
 */
final class OptionSettingsTruthiness {
	public static function isTruthy( $value ) {
		return (bool) $value;
	}
}
