# Phase 1 — Setup and Architecture

## Summary

This task collects the initial repository setup and core architecture work required before feature implementation. It establishes branch strategy, code layout, autoloading, bootstrap/service container, and developer workflows.

## Contract

- Inputs: repository root, existing source in `src/`, project `composer.json` (or create one), existing PHP namespace conventions.
- Outputs: documented branch strategy, `composer.json` with PSR-4 autoloading, skeleton service container/bootstrap, and a reference doc in `tasks/todo/setup-architecture/01-Setup-and-Architecture.md`.
- Success criteria: project builds (no syntax errors), autoload works for classes under `HtmlSocialShare\`, and README contains setup/branching instructions.

## Scope & Acceptance

- Initialize repository conventions (branch naming, PR policy).
- Add or update `composer.json` for PSR-4 autoloading.
- Create `bootstrap.php` (or `src/bootstrap.php`) that wires up autoloader and basic DI container registration.
- Provide developer setup notes in README (phpunit, phpcs, `composer install`, running tests).

## Files to create/update

- `composer.json` (create or update)
- `src/bootstrap.php` (new)
- `tasks/todo/setup-architecture/01-Setup-and-Architecture.md` (this file)
- `README.md` (add developer setup section)

## Implementation checklist

- [ ] Decide branch strategy and document (main/master, develop, feature/*, release/*)
- [ ] Add `composer.json` with PSR-4 mapping: `HtmlSocialShare\\` -> `src/`
- [ ] Add `src/bootstrap.php` that includes composer's autoload and returns a container instance placeholder
- [ ] Add minimal `phpunit.xml` (optional) and instructions in README
- [ ] Run a PHP lint pass to ensure no syntax errors

## Developer notes / Steps

1. Document branch strategy in `README.md` under "Branching & Workflow".
2. Create or update `composer.json` at project root:
   - Set name, description, require (php >=7.4 or as appropriate), autoload PSR-4 mapping.
3. Run `composer dump-autoload` and verify classes in `src/HtmlSocialShare` are autoloadable.
4. Add `src/bootstrap.php` that requires `vendor/autoload.php` and creates a simple DI container (or placeholder with comments linking to preferred container).
5. Commit changes on a feature branch (e.g., `feature/setup-psr4`) and open a PR.

Estimated time: 2 - 6 hours depending on composer presence and CI integration.

## Notes

- Keep changes minimal and non-breaking. If a `composer.json` already exists, prefer to update it rather than overwrite.
- If the project targets older PHP versions, adjust composer `platform` settings accordingly.

## History

- Created: 2025-09-27

---

## Ultra-Granular Task Breakdown

### Task Prioritization

#### Critical Path (Must Do First)

- PHASE1-001 to PHASE1-050: Repository setup, PSR-4 implementation, core classes (Weeks 1-2)
- PHASE1-051 to PHASE1-100: Data model migration, REST endpoints (Weeks 2-3)
- PHASE1-101 to PHASE1-150: Icon system foundation (Weeks 3-4)

#### High Priority (Do Next)

- PHASE1-151 to PHASE1-200: Security hardening, testing setup (Weeks 4-5)

### Estimated Timeline

- **Total Estimated Time**: 4-6 weeks (160-240 hours)
- **Critical Path**: 3 weeks (120 hours)
- **Can be parallelized**: PSR-4 setup and data migration

### Success Criteria

1. Repository initialized with PSR-4 structure
2. Core classes (ProfileManager, ShareRenderer, etc.) implemented
3. Data migration from legacy options completed
4. Basic REST endpoints functional
5. Icon registry sanitized and tested
6. CI/CD pipeline with linting and tests passing

---

### Phase 1A: Repository and PSR-4 Setup (40 hours)

#### PHASE1-001: Initialize repository and branch strategy (2 hours)

- Task: Set up Git repository with main, develop, feature/*, hotfix/* branches
- Success Criteria: Repository cloned, branches created, initial commit made
- Files: .git/, README.md
- Dependencies: None
- Estimated Time: 120 minutes
- Status: completed
- Implementation:
  - Create main branch
  - Create develop branch
  - Set up branch protection rules
  - Initial commit with basic structure

#### PHASE1-002: Implement PSR-4 autoloading (4 hours)

- Task: Set up PSR-4 namespace `HtmlShare` with Composer autoloader
- Success Criteria: Classes load correctly via namespace
- Files: `composer.json`, `src/Main.php`, `html-social-share.php`
- Dependencies: PHASE1-001
- Estimated Time: 240 minutes
- Status: in-progress
- Implementation:
  - Define top-level namespace `HtmlShare`
  - Configure Composer autoload
  - Register autoloader in main plugin file
  - Test class loading

#### PHASE1-003: Create service container bootstrap (3 hours)

- Task: Implement service container for dependency injection
- Success Criteria: Core classes instantiated via container
- Files: `src/Main.php`, `src/ServiceContainer.php`
- Dependencies: PHASE1-002
- Estimated Time: 180 minutes
- Status: not-started
- Implementation:
  - Design service container pattern
  - Bootstrap core classes
  - Implement dependency injection

#### PHASE1-004: Implement ProfileManager class (4 hours)

- Task: Create ProfileManager class for handling social profiles
- Success Criteria: Class handles CRUD operations for profiles
- Files: `src/ProfileManager.php`
- Dependencies: PHASE1-003
- Estimated Time: 240 minutes
- Status: not-started
- Implementation:
  - Define ProfileManager interface
  - Implement profile CRUD methods
  - Add validation and sanitization

#### PHASE1-005: Implement ShareRenderer class (4 hours)

- Task: Create ShareRenderer class for rendering share buttons
- Success Criteria: Renders HTML share buttons without JS
- Files: `src/ShareRenderer.php`
- Dependencies: PHASE1-003
- Estimated Time: 240 minutes
- Status: not-started
- Implementation:
  - Define ShareRenderer interface
  - Implement HTML rendering logic
  - Add URL template replacement

#### PHASE1-006: Implement IconRegistry class (4 hours)

- Task: Create IconRegistry class for managing icons
- Success Criteria: Handles builtin and custom icons
- Files: `src/IconRegistry.php`
- Dependencies: PHASE1-003
- Estimated Time: 240 minutes
- Status: not-started
- Implementation:
  - Define IconRegistry interface
  - Implement icon storage and retrieval
  - Add sanitization for custom SVGs

#### PHASE1-007: Implement Settings class (3 hours)

- Task: Create Settings class for admin options
- Success Criteria: Handles plugin settings with validation
- Files: `src/Settings.php`
- Dependencies: PHASE1-003
- Estimated Time: 180 minutes
- Status: not-started
- Implementation:
  - Define Settings interface
  - Implement option storage
  - Add input validation

#### PHASE1-008: Implement Cache class (3 hours)

- Task: Create Cache class for output caching
- Success Criteria: Caches rendered output using transients
- Files: `src/Cache.php`
- Dependencies: PHASE1-003
- Estimated Time: 180 minutes
- Status: not-started
- Implementation:
  - Define Cache interface
  - Implement transient-based caching
  - Add cache invalidation

### Phase 1B: Data Model and Migration (30 hours)

#### PHASE1-051: Design new option schema (2 hours)

- Task: Define `hss_core`, `hss_profiles`, `hss_icons` options
- Success Criteria: Schema documented and agreed upon
- Files: `docs/04-Data-Model-and-Options-Schema.md`
- Dependencies: None
- Estimated Time: 120 minutes
- Status: not-started
- Implementation:
  - Define `hss_core` structure
  - Define `hss_profiles` structure
  - Define `hss_icons` structure

#### PHASE1-052: Implement backward compatibility shim (4 hours)

- Task: Create migration scripts for legacy options
- Success Criteria: Legacy options migrate to new schema
- Files: `src/Migration.php`
- Dependencies: PHASE1-051
- Estimated Time: 240 minutes
- Status: not-started
- Implementation:
  - Map legacy options to new schema
  - Implement migration runner
  - Add rollback capability

### Phase 1C: REST Endpoints and Security (20 hours)

#### PHASE1-101: Implement basic REST endpoints (4 hours)

- Task: Create REST API for admin operations
- Success Criteria: CRUD endpoints for profiles and settings
- Files: `src/RestApi.php`
- Dependencies: PHASE1-004, PHASE1-007
- Estimated Time: 240 minutes
- Status: not-started
- Implementation:
  - Register REST routes
  - Implement CRUD operations
  - Add capability checks

#### PHASE1-102: Implement SVG sanitization (4 hours)

- Task: Add SVG sanitization unit with tests
- Success Criteria: Custom SVGs sanitized and safe
- Files: `src/SvgSanitizer.php`, `tests/SvgSanitizerTest.php`
- Dependencies: PHASE1-006
- Estimated Time: 240 minutes
- Status: not-started
- Implementation:
  - Implement DOM-based sanitizer
  - Add unit tests
  - Cover edge cases

#### PHASE1-103: Set up CI/CD pipeline (3 hours)

- Task: Configure GitHub Actions for linting and tests
- Success Criteria: CI passes on commits
- Files: `.github/workflows/ci.yml`
- Dependencies: None
- Estimated Time: 180 minutes
- Status: not-started
- Implementation:
  - Set up PHP_CodeSniffer
  - Configure PHPUnit
  - Add security scanning
