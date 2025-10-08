### Frontend Icon System Overview

This document defines the frontend icon system for Html Social Share Buttons following the unified data model. It covers icon storage and registry, runtime lookup and rendering, admin picker UX, sanitization and security, caching and performance optimizations, migration considerations, and the APIs/hooks for extensibility. The system is built for a no-JS-by-default server-rendered experience, with optional progressive enhancement when JS is enabled.

---

### Icon Data Model and Registry

#### Canonical records
- **hss_icons.sets** — plugin-provided sets and metadata.
- **hss_icons.custom** — user-supplied sanitized SVG entries.
- **profile.icon** — lightweight reference used by profiles:
  - source: "builtin" | "set:{id}" | "custom"
  - ref: icon_key | custom_id

#### Icon record shape
- id: string (stable id or sha256 hash)
- label: string
- source: string ('builtin'|'set:{id}'|'custom')
- svg: string|null (sanitized inline SVG for custom or set-provided svgs)
- viewbox: string|null
- default_fill: string|null (optional base color)
- tags: array[string]
- created: ISO8601 timestamp
- license: string|null
- sanitized_at: ISO8601 timestamp

#### Set record shape
- id: string
- name: string
- version: string
- license: string
- author: string
- icons: assoc array icon_key => icon_ref (icon_ref points to inline svg or a built-in asset)
- meta: array

#### Key design decisions
- Profiles reference icons by pointer, not by inlining large SVGs inside profile objects.
- Store sanitized SVGs in hss_icons.custom only after DOM-based sanitization and hashing.
- Keep plugin-provided sets shipped as compiled PHP arrays or JSON assets; also register them into hss_icons.sets on activation for consistent runtime lookup.
- Allow multiple sets concurrently; support global active_set in hss_core and per-profile overrides.

---

### Runtime Lookup and Rendering

#### Resolution algorithm
1. Load canonical profiles from cache (hss_profiles).
2. For each profile, read profile.icon.source/ref.
3. Resolve to an icon record:
   - source === "custom" => look up hss_icons.custom[ref].svg
   - source === "set:{id}" => find hss_icons.sets[id].icons[ref] then retrieve svg or built-in asset
   - source === "builtin" => treat as set:{builtin} fallback
4. If resolution fails, use a compact fallback icon (simple inline SVG stored in code).

#### Render output constraints
- Always output inline sanitized SVG markup when available to allow CSS fill/stroke styling and color theming.
- If only CSS classes are used for built-in icons (e.g., sprite or icon-font fallback), output an accessible element with aria-hidden and a sr-only label.
- Default markup example (server-rendered, no-JS):

```html
<a class="hss-profile-button hss-profile-twitter" href="https://x.com/example" target="_blank" rel="noopener noreferrer" aria-label="Follow on X">
  <span class="hss-icon-wrap" aria-hidden="true">
    <!-- inline sanitized svg here -->
    <svg viewBox="0 0 24 24" role="img" focusable="false"><path d="..."></path></svg>
  </span>
  <span class="screen-reader-text">Follow on X</span>
</a>
```

#### Attributes and accessibility
- aria-label equals profile.label or handle.
- Use role="img" and focusable="false" on SVGs for cross-browser compatibility.
- Provide optional visible label after icon controlled by profile.render options.
- Ensure link rel includes noopener noreferrer for new_tab targets; append noopener always when target="_blank".

#### Progressive enhancement
- Optional JS enhancement can replace inline SVG with animated SVGs, tooltips, or third-party icons. JS must be opt-in and loaded from an enqueued script with a capability flag and non-blocking async.

---

### Admin Icon Picker and UX

#### Picker core flows
- Global icon set selector: choose active icon set (affects default icons used by new profiles).
- Per-profile picker: pick from active set, switch to other installed sets, or upload custom SVG.
- Custom upload flow: single-file upload, client-side light validation (size, MIME), then POST to admin REST endpoint that sanitizes and stores sanitized SVG in hss_icons.custom and returns id + preview.
- Preview pane: shows final runtime rendering (how CSS variables and theme colors will apply).

#### UX details
- Search and filter by tag, name, or set.
- Show license and author for set icons.
- Provide “use default set icon” toggle to switch profile to global set changes.
- Bulk assign: allow changing icon for multiple profiles at once.
- Import/export: allow exporting selected custom icons as a ZIP of sanitized SVGs and importing sanitized SVGS via admin tool.

#### Admin endpoints and security
- REST endpoints under /wp-json/hss/v1/icons: list, create (upload), delete, update metadata.
- Capability checks: manage_options or hss_manage_icons.
- CSRF protection via WP nonces and strict MIME checking on uploads.
- Rate-limit large uploads and reject oversized SVGs (e.g., > 100KB by default configurable).

---

### Sanitization and Security

#### Sanitization pipeline
1. Client-side basic checks: file type image/svg+xml, max file size.
2. Server-side DOM sanitization:
   - Load via DOMDocument with LIBXML_NONET and LIBXML_NOBLANKS.
   - Remove disallowed elements: script, foreignObject, iframe, object, embed.
   - Remove attributes starting with on* and any javascript: URIs.
   - Strip external resource references by default (xlink:href or image src with http(s) flagged). Optionally allow data: images after strict checks.
   - Enforce attribute whitelist per tag (path, svg, g, circle, rect, line, polygon, polyline, ellipse, title, desc, use).
3. Run wp_kses as final filter using a minimal SVG whitelist.
4. Compute and store sha256 hash; store sanitized_at timestamp.
5. Reject if sanitized content is empty or contains suspicious patterns.

#### Security hardening
- Never eval or unserialize user-supplied SVGs.
- Limit uploaded SVG complexity (limit node count and attribute counts) to mitigate ReDoS or parser exhaustion.
- Log rejected uploads for admin reporting.
- Apply capability checks and nonces for all mutating endpoints.
- Use prepared SQL or WP API for any database interaction; avoid direct DB queries where possible.

#### Policy for external references
- Disallow remote http(s) external images inside SVG by default.
- Offer an advanced setting to allow external refs only for trusted hosts and with explicit admin consent.

---

### Caching, Performance, and Delivery

#### Caching layers
- In-memory object cache: cache resolved icon markup per icon id (key: hss_icon_markup:{id}).
- Profiles cache: cache final ordered profiles list (key: hss_profiles_render:{positions_hash}).
- Precompile inactivation: when hss_icons or hss_profiles update, invalidate relevant keys.

#### Precompilation optimizations
- On icon set activation or custom upload, optionally pre-generate:
  - minified inline SVG snippets (strip whitespace, remove metadata) for quick echo.
  - a combined SVG sprite or a compiled PHP map of inline SVG strings for fast server-side access.
- For sites with many icons, offer a build task that compiles selected icon sets into a static JSON/PHP asset file to avoid option unserialization on every request.

#### Network delivery
- Inline SVG is preferred for no-JS UX and full CSS control. For extreme scale or CDN strategies:
  - Provide optional sprite endpoint that returns SVG sprite served as static file via plugin assets or object-cache backed endpoint.
  - For icon-font fallback, ship only as opt-in and avoid loading large font files by default.

#### Performance guardrails
- Limit custom icons per site by default (configurable).
- Throttle large batch imports and paginate admin lists.
- Use lazy-loading for admin previews when >100 icons.
- Benchmark common render paths and keep render cost under a small fixed CPU/IO budget per page.

---

### Migration and Backward Compatibility

#### Mapping legacy settings
- Legacy enabled networks -> create hss_profiles share entries with visible flag.
- Legacy title, toggles, positions -> migrate into hss_core fields.
- Legacy icon handling (if any) -> attempt to map to builtin references; if custom svg found, sanitize and store in hss_icons.custom and point profile to its id.

#### Migration mechanics
- Implement idempotent migration runner with:
  - backup of legacy option under hss_legacy_backup (expire 30 days).
  - detailed admin migration report showing counts, skipped items, and actions required.
  - toggle to re-run migration on admin request for debugging.
- Provide compatibility shim functions that resolve from hss_core/hss_profiles but fall back to legacy options when the new option is missing.

#### Versioning and rollout
- Add hss_icons.sets.version to detect breaking changes in built-in sets. If a built-in set is updated with breaking key changes, create a migration map and notify admins in the dashboard with suggested remapping.

---

### APIs, Filters, and Testing

#### Public APIs and filters
- Filters:
  - hss_icon_resolve($icon_ref, $context) — return resolved icon markup or null.
  - hss_profile_render_attributes($profile, $attrs) — allow third parties to modify attributes before render.
  - hss_icons_pre_sanitize($raw_svg) — allow plugins to run additional checks; must not bypass core sanitization.
- Actions:
  - hss_icon_uploaded($icon_id, $meta)
  - hss_profile_updated($profile_id, $profile)
- REST endpoints:
  - GET /hss/v1/icons — list icons with paging and search.
  - POST /hss/v1/icons — upload and sanitize custom SVG.
  - DELETE /hss/v1/icons/{id} — delete custom icon.

#### Tests and CI
- Unit tests:
  - icon resolution logic, fallback behavior, and built-in mapping.
  - sanitizer: inputs with scripts, event handlers, external refs, data URIs, malformed XML.
- Integration tests:
  - render output correctness for typical profiles and positions.
  - migration runner idempotence and mapping accuracy.
- Security tests:
  - fuzz tests for SVG parser to detect DoS inputs.
  - dependency scanning and CodeQL/Static analysis for plumbing.
- Visual tests:
  - snapshot tests for icon rendering (headless Chrome) for core themes and sizes.

---

### Implementation Checklist

- [ ] Register built-in icon sets into hss_icons.sets on activation.
- [ ] Implement DOM-based sanitization function with attribute whitelist and complexity limits.
- [ ] Build REST endpoints for icon listing/upload/delete with capability checks.
- [ ] Create admin icon picker UI with search, preview, and upload.
- [ ] Implement runtime resolver with caching and precompiled assets support.
- [ ] Add migration runner mapping legacy icon/network settings to new profiles/icons.
- [ ] Add filters/actions and document extension points.
- [ ] Add unit, integration, fuzz, and visual tests to CI.

---

This Frontend Icon System gives you a unified, secure, and performant way to manage both plugin-provided and user-supplied icons for frontend and backend use, while preserving a no-JS-by-default server-rendered experience and a clear path for progressive enhancement and future integrations.