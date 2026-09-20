# Next-release plan: URL safety and local sharing

Status: proposed planning note. It does not authorize implementation, tagging,
WordPress.org upload, or deployment.

Detailed implementation plan: [`2026-09-20-next-release-detailed-plan.md`](2026-09-20-next-release-detailed-plan.md).

## Outcome

Ship a small release that makes HSSB’s existing local share links predictable:
one documented URL-source contract, correct query-component encoding, an
opt-in browser URL enhancement in Advanced options, preserved legacy behavior,
and no required third-party frontend SDK or share-count request.

## Scope

1. **Document and test URL precedence.** Preserve explicit caller URLs and the
   current loop-post contract. Define singular, archive, search, front-page,
   AJAX/editor, and final current-request fallback behavior before changing the
   resolver.
2. **Harden the URL boundary.** Keep URL/title/description/image values
   structured until final query construction. Encode each query value exactly
   once, then escape the complete HTML attribute. Add vectors for Unicode,
   ampersands, fragments, quotes, existing queries, and already-encoded input.
3. **Preserve compatibility.** Exercise shortcode, block, widget, builder,
   automatic placement, legacy API, and existing icon/network URL contracts.
4. **Add the Advanced browser URL option.** Add a persisted
   `use_browser_url` setting, defaulting to off. When enabled, page-level share
   links may use `window.location.href` at click time. Explicit custom URLs and
   post-specific/loop-post contexts always remain authoritative. The server
   generated `href` remains the no-JS fallback. Use one shared template/data
   contract so JavaScript does not create a second network map.
5. **Keep the default path local.** Verify that ordinary frontend output adds
   no remote SDK, count request, tracking beacon, cookie, or remote asset.
6. **Defer other public features.** Copy Link, native Web Share, and new
   networks remain separate follow-up work until this option and the resolver
   matrix are proven.

## Explicitly out of scope

Share counts, link shortening, hosted provider SDKs, click analytics, telemetry,
Copy Link, native Web Share, and broad network expansion. Mastodon requires a
separate endpoint decision.

## Acceptance gates

- Focused URL and rendering tests pass for all documented contexts.
- Existing final-anchor and once-only-encoding contracts remain green.
- The setting is false for fresh, sparse, legacy, and malformed settings unless
  explicitly enabled.
- Explicit custom URLs and archive-loop post URLs are unchanged when the option
  is enabled.
- Browser checks cover click-time URL resolution, history API changes, query
  strings, fragments, keyboard/focus, accessible names, no-JS fallback, and
  mobile layout.
- Default frontend request capture shows no unexpected third-party runtime.
- The exact candidate archive is rebuilt and its SHA-256 recorded before any
  release authorization.

Research basis: [`research/wporg-share-plugins-2026-09-20/findings.md`](../../research/wporg-share-plugins-2026-09-20/findings.md). The ten-plugin source review is preserved in that directory and its two Luna reports.
