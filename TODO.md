# Deferred features

These items are intentionally outside the next release scope. They need their
own compatibility, privacy, and runtime evidence before implementation.

## URL and sharing behavior

- [ ] Add a user-facing URL mode (`permalink`, `current request`, or `custom`)
      only after the source-precedence contract is documented across shortcodes,
      blocks, widgets, builders, AJAX, and legacy calls.
- [ ] Add an explicit query-parameter policy for functional arguments versus
      tracking arguments. Do not strip `utm_*`, `fbclid`, `gclid`, or similar
      values by default until preview/editor and repeated-key cases are tested.
- [ ] Add Copy Link with a real link fallback, Clipboard API enhancement,
      keyboard support, and screen-reader status feedback.
- [ ] Add native Web Share as an optional progressive enhancement with a
      deterministic direct-link fallback.

## Network coverage

- [ ] Add one or two modern networks after the URL contract is stable (candidate
      set: Reddit, Threads, WhatsApp, or Mastodon).
- [ ] Define Mastodon behavior before implementation: direct instance URL,
      user-provided instance, or an intermediary. Do not hide this decision in
      a static template.
- [ ] Complete the full network addition matrix: templates, query encoding,
      icons, legacy identifiers, defaults, labels, translations, builders,
      docs, and archive tests.

## Optional integrations

- [ ] Design an opt-in, cached share-count module with strict timeouts and no
      render-blocking requests.
- [ ] Consider link shortening only with verified TLS, scoped credentials, and
      an explicit privacy contract.
- [ ] Consider provider/hosted SDK adapters only as opt-in integrations with
      consent, domains, loading behavior, and a local fallback documented.
- [ ] Do not add click analytics or telemetry to the default share output.

## Evidence needed before deferred work

- [ ] WordPress runtime matrix for singular, loop, archive, search, front-page,
      AJAX, and custom URL contexts.
- [ ] Browser checks for Unicode, ampersands, fragments, existing query strings,
      already-encoded values, keyboard use, and no-JS behavior.
- [ ] Network-request capture proving default output has no unexpected SDK,
      count, analytics, cookie, or remote-asset request.

Research basis: [`research/wporg-share-plugins-2026-09-20/findings.md`](research/wporg-share-plugins-2026-09-20/findings.md), [`reviews/set-a.md`](research/wporg-share-plugins-2026-09-20/reviews/set-a.md), and [`reviews/set-b.md`](research/wporg-share-plugins-2026-09-20/reviews/set-b.md).
