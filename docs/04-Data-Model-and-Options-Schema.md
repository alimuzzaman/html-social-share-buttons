### Summary

This document defines a unified data model and migration plan to move the Html Social Share Buttons plugin from its legacy option layout to a PSR-4 friendly, future-proof option format that supports:
- server-rendered share buttons and follow/profile buttons,
- plugin-provided icon sets and user-supplied icons (SVG),
- frontend and backend usage from a single canonical source,
- efficient caching, sanitization, and feature flags.

It recommends keeping an option-first model (single canonical option + separate icon registry option) with a documented migration path and a clear upgrade-to-custom-table plan for very large installs.

---

### Backward Compatibility Shim Layer

Implement migration scripts for legacy option structures to ensure smooth upgrade from previous versions.

---

### New option schema (canonical storage)

Store two main option records:
- hss_core (plugin settings, flags, UI defaults)
- hss_profiles (profiles & share-network definitions keyed by id)
- hss_icons (icon set registry and custom icons)

Example JSON (stored as PHP array via update_option):

```php
// hss_core
[
  'version' => '2.0.0',
  'title' => 'Share this with your friends',
  'positions' => ['left', 'before_post'], // canonical position keys
  'auto_hide' => true,
  'use_port' => false,
  'google_social_analytics' => false,
  'nofollow' => true,
  'enabled_networks' => ['facebook','twitter','linkedin','pinterest','email'],
  'legacy_migration' => ['done' => true, 'from_version' => '1.4.3', 'date' => '...']
]

// hss_profiles
[
  'facebook' => [
    'id'=>'facebook',
    'type'=>'share', // share|profile
    'label'=>'Share on Facebook',
    'handle'=>'facebook',
    'url_template'=>'https://www.facebook.com/sharer/sharer.php?u={url}&t={title}',
    'visible'=>true,
    'new_tab'=>true,
    'order'=>0,
    'icon' => ['source'=>'builtin','ref'=>'facebook'],
    'meta'=> ['analytics' => true]
  ],
  'twitter' => [ ... ],
  'follow_twitter' => [
    'id'=>'follow_twitter',
    'type'=>'profile',
    'label'=>'Follow on X',
    'handle'=>'twitter',
    'url'=>'https://twitter.com/example',
    'visible'=>true,
    'icon' => ['source'=>'custom','ref'=>'svg_hash_abc'],
  ]
]

// hss_icons
[
  'sets' => [
    'builtin' => [
      'id'=>'builtin',
      'name'=>'Default outline',
      'version'=>'1.0',
      'icons' => ['facebook'=>'builtin_facebook','twitter'=>'builtin_twitter', ...],
      'meta'=>['license'=>'MIT','author'=>'HSS']
    ],
    'feather' => [ ... ]
  ],
  'custom' => [
    'svg_hash_abc' => [
      'id'=>'svg_hash_abc',
      'label'=>'My Twitter',
      'svg'=>'<svg ...>...</svg>',
      'viewbox'=>'0 0 24 24',
      'tags'=>['brand','rounded'],
      'created'=> '...'
    ]
  ]
]
```

Key principles:
- Canonical single source of truth for rendering: hss_profiles drives frontend output.
- Icons are referenced by tuple {source: builtin|set_id|custom, ref: icon_key|custom_id}.
- Profiles carry either a url (profile) or url_template (share) to allow server-render replacement of {url},{title} etc.

---

### Icon data model (sets and icons)

Design a small, explicit schema that supports plugin-provided sets, user-imported single SVGs, and remote/icon fonts later.

Icon Set record:
- id (string) — unique set id (builtin, feather, kit_1)
- name (string)
- version (string)
- license (string)
- author (string)
- icons (assoc array: icon_key => builtin_ref) — mapping keys to internal names
- meta (arbitrary)

Individual Icon record (for custom or preloaded inline sets):
- id (string) — stable id or hash (e.g., svg_sha256)
- label (string)
- source (string) — 'builtin'|'set:{id}'|'custom'
- svg (string|null) — sanitized inline svg when source === custom or set provides inline svgs
- viewbox (string|null)
- default_fill (string|null)
- tags (array)
- created (timestamp)
- license (string|null)
- sanitized_at (timestamp)

Icon lookup strategy:
1. Profile.icon.source === 'builtin' or 'set:{id}' => get svg/markup from built-in registry (code assets or compiled PHP array).
2. Profile.icon.source === 'custom' => read inline sanitized svg from hss_icons.custom by id/ref.
3. Provide a fallback icon (generic link) if not found.

Why separate registry:
- Centralizes sanitization and deduplication.
- Enables caching and sprite precompilation.
- Keeps profile records small (reference vs storing large SVGs inline inside profiles).

---

### How icon sets map into options after user chooses them

- When user selects a plugin-provided set:
  - Save selection in hss_core as 'active_icon_set' => 'feather'.
  - Profiles referencing builtin names continue to refer to icon keys; render resolves keys against active set at runtime.
- When user assigns a specific icon to a profile from a set:
  - Save profile.icon = ['source'=>'set:feather','ref'=>'twitter'].
- When user uploads a custom icon:
  - Sanitize, store sanitized svg in hss_icons.custom keyed by hash id.
  - Save profile.icon = ['source'=>'custom','ref'=>'svg_hash_abc'].
- When the plugin updates or replaces a builtin set, maintain backward-compatible icon_key names and record set.version in hss_icons.sets so migration can map old keys.

UX note:
- Admin icon picker shows: Active sets (choose global); per-profile "use set icon" or "upload custom SVG" or "pick from other sets".
- Provide "preview" and "test render" using sanitized output.

---

### Mapping existing current settings into the new model

Map each current option to the new schema:

- Text Field
  - Enter a Title -> hss_core.title
  - Exclude by Page ID/Title/Slug -> hss_core.exclusions => ['ids'=>[], 'slugs'=>[], 'titles'=>[]] (store more structured)

- Toggles (Advanced Options)
  - Google Social analytics -> hss_core.google_social_analytics (boolean)
  - Auto hide button -> hss_core.auto_hide (boolean) with optional auto_hide.position => ['left','right'] override
  - Use port on the url -> hss_core.use_port (boolean) and hss_core.port_value (int, optional)
  - No follow social link -> hss_core.nofollow (boolean)

- Where to show buttons
  - Show on Left Side -> hss_core.positions includes 'left'
  - Show on Right Side -> include 'right'
  - Show Before Post -> include 'before_post'
  - Show After Post -> include 'after_post'
  (Positions normalized to canonical keys: left|right|before_post|after_post; additional future values are 'widget', 'manual_shortcode', etc.)

- Select -> Enable/Disable Social Networks
  - Convert each enabled network into a profile in hss_profiles (type: share), with default url_template and builtin icon mapping and visible=true.
  - If previously stored as booleans, migrate to hss_profiles entries and set visible flag accordingly.

Migration mapping summary (legacy->new):
- legacy_title -> hss_core.title
- legacy_exclude -> hss_core.exclusions
- legacy_toggles -> corresponding booleans in hss_core
- legacy_positions -> hss_core.positions array
- legacy_enabled_networks -> create/update hss_profiles for each network; set profile.visible per previous boolean

---

### Migration strategy and implementation plan

High level steps:
1. Add a migration runner on plugin activation or upgrade `hss_migrate_legacy_options()`.
2. Mark migration idempotent and store hss_core.legacy_migration flag with from_version and date.
3. Read legacy options; validate and normalize values.
4. Build target records: hss_core, hss_profiles, hss_icons (populate builtin set entry).
5. For each legacy enabled network:
   - create profile with canonical id (lowercase) and default url_template, builtin icon reference, visible flag.
6. Move exclusions, toggles, title into hss_core.
7. Validate and sanitize any legacy custom SVGs (if any) before copying.
8. Write new options atomically: use update_option for each, then set migration flag.
9. Add fallback layer (compat shim) to read legacy fields if hss_core missing while migration in progress.
10. Log migration events via transient or option for debugging.

Migration pseudo-code (PHP):

```php
function hss_migrate_legacy_options() {
  if (get_option('hss_core')['legacy_migration']['done'] ?? false) {
    return;
  }
  $legacy = get_option('html_social_share_options', []);
  $hss_core = [
    'version'=>'2.0.0',
    'title'=> sanitize_text_field($legacy['title'] ?? 'Share this'),
    'positions'=> normalize_positions($legacy),
    'auto_hide'=> !!($legacy['auto_hide'] ?? false),
    // ...
  ];
  $profiles = [];
  foreach (['facebook','twitter','linkedin','google_plus','pinterest','email'] as $net) {
    $enabled = ! empty($legacy['enable_' . $net]);
    $profiles[$net] = [
      'id'=>$net,
      'type'=>'share',
      'label'=>ucfirst($net),
      'url_template'=> get_default_template($net),
      'visible'=>$enabled,
      'icon'=> ['source'=>'builtin','ref'=>$net],
    ];
  }
  update_option('hss_core', $hss_core);
  update_option('hss_profiles', $profiles);
  update_option('hss_icons', get_builtin_icon_registry());
  update_option('hss_migration_log', ['from'=>'legacy_v1','date'=>current_time('mysql')]);
}
```

Rollback and verification:
- Keep legacy option copy under hss_legacy_backup for 30 days before deleting (or until next major release).
- Provide admin-facing migration report (counts, warnings for custom SVGs skipped, mapping details).
- Mark migration as complete only after self-checks (profiles count matches expected).

---

### Frontend and backend usage (unified model)

Rendering flow (server-side, no-JS default):
1. Theme or shortcode calls HSS_Renderer::render(position, args).
2. Renderer loads hss_core (positions, flags) and hss_profiles (ordered, visible).
3. For each profile, resolve icon via hss_icons lookup and output sanitized markup: inline SVG or CSS class for builtin.
4. Replace placeholders in profile.url_template ({url},{title},{excerpt}) server-side and escape with esc_url/esc_html.
5. Output accessible markup (aria-labels, sr-only labels) and no inline event handlers.

Backend CRUD:
- Profiles are managed via admin UI that reads/writes hss_profiles.
- Icons managed via icon picker UI that reads/writes hss_icons.
- All mutating endpoints require capability checks, nonces, and sanitize data server-side.

APIs and hooks:
- Filters: hss_profiles_preload, hss_icon_resolve($icon_ref,$context), hss_profile_render_attributes($profile,$attrs)
- Actions: hss_profile_added, hss_profile_updated, hss_profile_deleted, hss_migration_completed

Example filter usage:
- A third-party plugin can filter a profile URL before rendering:
  add_filter('hss_profile_render_url', function($url, $profile) { return my_shortener($url); }, 10, 2);

---

### Implementation notes: security, caching, and testing

Sanitization:
- Sanitize all input server-side using:
  - sanitize_text_field for handles/labels,
  - esc_url_raw for stored URLs,
  - DOM-based sanitization + wp_kses whitelist for user-supplied SVGs,
  - limit SVG upload size and complexity (reject scripts, external refs unless explicitly allowed).
- Store sanitized SVGs only; do not re-sanitize on render (but verify integrity via stored hash).

Security controls:
- Capability checks (manage_options or custom capability) for admin changes.
- Nonces for admin AJAX/REST endpoints.
- Validate and escape all outputs with esc_html/esc_url.
- Scan dependencies and add CodeQL or Snyk to CI.

Caching strategy:
- Use wp_cache_set / wp_cache_get with key per option (e.g., 'hss_profiles_v2') to avoid repeated option unserialization.
- Use transient for icon sprite generation results.
- On update of profiles/icons/core, invalidate related caches.
- Consider precompiling an optimized icon sprite or inlined minified SVG bundle at save time for fast render.

Performance & scale:
- Option storage is fine for typical sites; implement migration-to-table plan:
  - Create custom table hss_profiles with indexes for high-count installs.
  - Provide migration script to move options -> table.
- Use pagination for admin lists when >1000 icons/profiles.

Testing:
- Unit tests for migration runner (idempotence, mapping correctness).
- Integration tests for renderer (placeholders replacement, escaping).
- Fuzz tests for SVG sanitization and DOM parsing.
- Visual regression tests (snapshot of markup + icons) for smoke checks.
- Add CI gates for PHPCS, PHPStan/Psalm, unit tests, and security scanner.

---

### Deliverables checklist (next actions to implement)

- finalize canonical option arrays and PHP constants (option names)
- implement migration runner and admin migration report
- implement hss_icons registry and sanitization unit
- implement profile CRUD and REST endpoints with capability & nonce validation
- implement server-render renderer that resolves icons and templates
- add caching invalidation logic and unit tests for each step
- prepare docs for plugin authors to extend via filters/actions

This model preserves backward compatibility while giving a clean, extensible structure for icons, profiles, and global settings. Implement migration as an atomic, auditable operation and keep the icon registry separate from profiles for reusability and performance.