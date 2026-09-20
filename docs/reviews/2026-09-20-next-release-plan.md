# Next-release plan: URL safety and local sharing

Status: proposed planning note. It does not authorize implementation, tagging,
WordPress.org upload, or deployment.

## Outcome

Ship a small release that makes HSSB’s existing local share links predictable:
one documented URL-source contract, correct query-component encoding, preserved
legacy behavior, and no required third-party frontend SDK or share-count
request.

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
   Do not add a global URL-mode setting in this release unless its effect on
   every adapter is explicitly specified.
4. **Keep the default path local.** Verify that ordinary frontend output adds
   no remote SDK, count request, tracking beacon, cookie, or remote asset.
5. **Prepare one follow-up feature.** After the resolver matrix passes, choose
   either Copy Link or one modern network adapter. Keep it behind the same
   context/encoding pipeline and provide a real no-JS fallback.

## Explicitly out of scope

Share counts, link shortening, hosted provider SDKs, click analytics, telemetry,
and broad network expansion. Mastodon requires a separate endpoint decision.

## Acceptance gates

- Focused URL and rendering tests pass for all documented contexts.
- Existing final-anchor and once-only-encoding contracts remain green.
- Browser checks cover keyboard/focus, accessible names, no-JS fallback, and
  mobile layout where the feature changes markup.
- Default frontend request capture shows no unexpected third-party runtime.
- The exact candidate archive is rebuilt and its SHA-256 recorded before any
  release authorization.

Research basis: [`research/wporg-share-plugins-2026-09-20/findings.md`](../../research/wporg-share-plugins-2026-09-20/findings.md). The ten-plugin source review is preserved in that directory and its two Luna reports.
