# Threat Model Matrix (Draft)

This document is a lightweight threat model matrix for the Html Social Share Buttons plugin. It captures assets, threats, likelihood, impact, and mitigations.

| Asset | Threat | Likelihood | Impact | Mitigation |
|---|---|---:|---:|---|
| SVG icon rendering | Malicious SVG with script/JS/xlink to exfiltrate data | Medium | High | Sanitize SVG; remove scripts/foreignObject; CSP; validate mime types |
| Admin icon upload | Upload of polyglot file or oversized payload | Low | Medium | Validate file type, size limits, server-side scanning, store outside webroot |
| REST endpoints | CSRF or unauthorized access | Low | High | Require capability checks, nonce, authentication for modifying endpoints |
| Option migration | Data loss during back-compat migration | Low | Medium | Back up options, run migrations in a transaction, provide rollback path |
| CLI scripts | Local file read/execution exposing sensitive paths | Low | Medium | Require explicit path args, do not run as web user, validate paths |

Next steps:
- Expand each row with threat model details (actor, attack vectors, detection, recovery)
- Add tests that exercise SVG sanitization with fuzz inputs
- Add CI gating for high-severity findings
