---
title: Design: New Option Schema (canonical storage)
---

Goal
----

Design a canonical option storage format for Html Social Share Buttons that:

- Supports versioned option schema.
- Stores profiles, iconsets, placements, and global settings in a single, extensible object.
- Provides a migration path from the legacy option keys used by earlier plugin versions.

Proposed canonical shape
-----------------------

Top-level option key: `hssb_options_v1`

Value (JSON / PHP array):

```json
{
  "version": 1,
  "profiles": {
    "1": { "network": "twitter", "handle": "@example", "enabled": true },
    "2": { ... }
  },
  "iconsets": {
    "default": { "selected": true, "style": "square" }
  },
  "placements": {
    "post": { "enabled": true, "position": "left" }
  },
  "settings": {
    "show_after_post": false,
    "default_networks": ["twitter","facebook"]
  }
}
```

Migration and backward compatibility
-----------------------------------

- Provide a `BackCompat` shim responsible for reading legacy option keys and mapping them into the canonical structure on first-load or when explicitly requested.
- The shim must be idempotent and only migrate values; it must not delete legacy keys (unless explicitly requested by user or admin action).
- The shim will expose an API `migrate(): array` returning the canonical options and a `mapLegacy(string $key)` helper for targeted mapping.

Storage strategy
----------------

- Persist the canonical structure as a single option `hssb_options_v1` (or a versioned key for future migrations).
- Keep legacy keys intact until migration is validated.
- Provide unit-tests and an admin-facing migration UI (later phases).

Acceptance criteria
-------------------

- `hssb_options_v1` exists and contains normalized profiles and settings.
- `BackCompat::migrate()` returns canonical array and does not throw.
- A small smoke-test demonstrates mapping of at least one legacy key.
