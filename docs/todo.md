# Project TODOs

php 5.6+ compatible
must work with latest php version
wordpress 5+


## Priority checklist to start coding

- Init repo and branch strategy: main, develop, feature/, hotfix/.
- PSR-4 refactor plan: top-level namespace, service container bootstrap, and core classes list (ProfileManager, ShareRenderer, IconRegistry, Settings, Cache).
- SVG sanitization unit implemented and covered by tests.
- Basic REST endpoints and capability mapping for admin operations.
- Backward compatibility shim layer for legacy option structures and migration scripts.
- Stub connectors for BetterLinks: capability to import/export links, use BetterLinks short URLs when available, and a clear plan to accept pro source code later.

## Security and testing items to include

- Threat model and attack surface matrix (XSS via SVG, CSRF on admin endpoints, capability escalation, unsafe deserialization).
- Automated security tests: static analysis (Psalm/PHPStan), dependency scanning (Dependabot/GitHub CodeQL), and unit tests asserting sanitized outputs.
- Fuzz tests for SVG parser and DOM-based sanitizer to catch malformed input.
- CI gating: block merges on PHPCS, failing tests, or security alerts.
- Security disclosure and responsible disclosure process file in repo.

## Acceptance criteria and next steps

- Minimum Viable Start: repo skeleton, PSR-4 boot, ProfileManager class, basic REST endpoints, sanitized icon registry, and CI with lint/tests passing.

- Definition of Done for phase 1: all CI checks green, unit tests cover core classes ≥ 70%, server-rendered shortcode outputs correct, and security scan shows no critical findings.

- Provided inputs required: final decision on data storage choice and the BetterLinks pro source when implementing tighter integration.

- Next step: create the repo tree and file stubs or implement the ProfileManager class; choose one now to start execution.


