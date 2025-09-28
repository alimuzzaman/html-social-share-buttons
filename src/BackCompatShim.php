<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\ArrayUtils;

class BackCompatShim implements BackCompatInterface
{
    protected $settings;

    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function migrate(): array
    {
        // Start from any existing canonical options
        $canonical = $this->settings->get('hssb_options_v1', [
            'version' => 1,
            'profiles' => [],
            'iconsets' => [],
            'placements' => [],
            'settings' => [],
        ]);

        // Example legacy keys mapping
        $legacyToCanonical = [
            'hssb_profiles' => 'profiles',
            'hssb_settings' => 'settings',
            'hssb_iconset' => 'iconsets.default',
            'hssb_social_links' => 'profiles', // older key used for social links
        ];

        foreach ($legacyToCanonical as $legacy => $path) {
            $val = $this->settings->get($legacy, null);
            if ($val !== null) {
                // Normalize: if the legacy value is a WP-style associative list of links,
                // try to coerce it into a numeric-indexed profiles array.
                if ($path === 'profiles' && is_array($val) && ArrayUtils::isAssociative($val)) {
                    $val = array_values($val);
                }

                // Read existing value at path using pure ArrayUtils helpers
                $existing = ArrayUtils::get($canonical, $path, null);

                if (is_array($existing) && is_array($val)) {
                    // Merge arrays preserving canonical structure
                    $merged = ArrayUtils::deepMerge($existing, $val);
                    $canonical = ArrayUtils::set($canonical, $path, $merged);
                } else {
                    $canonical = ArrayUtils::set($canonical, $path, $val);
                }
            }
        }

        // Persist canonical back for this shim (non-destructive)
        $this->settings->set('hssb_options_v1', $canonical);

        return $canonical;
    }

    public function mapLegacy(string $key): ?string
    {
        $map = [
            'hssb_profiles' => 'profiles',
            'hssb_settings' => 'settings',
            'hssb_iconset' => 'iconsets.default',
        ];

        return $map[$key] ?? null;
    }
}
