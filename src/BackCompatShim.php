<?php
namespace HtmlSocialShare;

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
                if ($path === 'profiles' && is_array($val) && $this->looksLikeAssociativeMap($val)) {
                    $val = array_values($val);
                }

                $this->applyPath($canonical, $path, $val);
            }
        }

        // Persist canonical back for this shim (non-destructive)
        $this->settings->set('hssb_options_v1', $canonical);

        return $canonical;
    }

    public function mapLegacy(string $key)
    {
        $map = [
            'hssb_profiles' => 'profiles',
            'hssb_settings' => 'settings',
            'hssb_iconset' => 'iconsets.default',
        ];

        return $map[$key] ?? null;
    }

    protected function applyPath(array & $target, string $path, $value)
    {
        $parts = explode('.', $path);
        $ref = & $target;
        foreach ($parts as $part) {
            if (!isset($ref[$part]) || !is_array($ref[$part])) {
                $ref[$part] = [];
            }
            $ref = & $ref[$part];
        }

        // If target is an array and value is array, merge
        if (is_array($ref) && is_array($value)) {
            $ref = array_merge($ref, $value);
        } else {
            $ref = $value;
        }
    }

    protected function looksLikeAssociativeMap(array $arr): bool
    {
        // Heuristic: if keys are strings, consider associative map
        foreach (array_keys($arr) as $k) {
            if (is_string($k)) {
                return true;
            }
        }
        return false;
    }
}
