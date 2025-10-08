# Copilot / Coding Agent Instructions

## ⚠️ Critical Context: Phase 1 Ground-Up Rewrite

**This WordPress plugin is being completely rewritten**. ALL existing code lives in `archive/` and `archive2/` directories. The root is empty except for documentation.

**MANDATORY FIRST STEPS:**
1. Read `documentation/START-HERE.md` (5 min overview)
2. Read `documentation/GETTING-STARTED.md` (setup requirements)
3. Read `.github/prompts/phase1-rewrite-foundation.prompt.md` (complete 33-task plan)
4. Check `documentation/PROGRESS-TRACKER.md` (track your work)

## 🏗️ Target Architecture (To Be Built)

```
Root (currently empty - to be created):
├── src/                         # PSR-4: HtmlSocialShare\
│   ├── Core/Plugin.php          # Main plugin bootstrap
│   ├── IconSystem/              # Icon registry, CSS generation
│   ├── Renderers/               # HTML button rendering
│   └── Options/                 # Settings management
├── react-src/
│   ├── admin-ui/                # React admin (Tailwind CSS)
│   └── iconsets/{iconset}/      # CSS templates (source)
├── build/
│   ├── admin-ui/                # Compiled React
│   └── iconsets/{iconset}/      # Generated CSS
├── assets/iconset/{iconset}/    # PNG icons (author-uploaded)
└── tests/                       # PHPUnit + Playwright

Currently archived:
├── archive/                     # v2.2.1 production code
└── archive2/                    # Failed rewrite attempt (learn from mistakes)
```

## 🔧 System Requirements & Tools

- **PHP**: 5.6-8.5+ (broad compatibility required)
- **Package Manager**: `pnpm` ONLY (never npm/yarn)
- **WordPress Test**: `wp-env` (required)
- **Node.js**: 18+
- **Composer**: For PSR-4 autoloading

### Essential Commands

```bash
# Setup
pnpm install && composer install
wp-env start

# Development  
pnpm run dev                    # Watch mode
pnpm run build                  # Production
pnpm run build:iconsets         # Compile iconset CSS

# Testing (TDD required!)
composer test                   # PHP unit + integration
pnpm test                       # JS tests
pnpm run test:e2e              # Playwright E2E
pnpm run test:visual           # Visual regression

# Linting
pnpm run lint:js:fix
composer run lint:fix
```

## 🎨 Icon System Architecture (CRITICAL)

**The plugin uses PNG icons with CSS background-image (NO SVG in frontend):**

```
Source (author creates):
  react-src/iconsets/{iconset}/
    ├── variables.css            # Colors, sizes
    ├── template.css             # CSS rules
    └── README.md

Build (automated):
  pnpm run build:iconsets →
  build/iconsets/{iconset}/style.css

Runtime (author uploads):
  assets/iconset/{iconset}/
    ├── facebook.png
    └── twitter.png

Frontend loads: build/iconsets/{iconset}/style.css
CSS references: assets/iconset/{iconset}/*.png
```

**Rules:**
- Frontend icons: ONLY PNG from `assets/iconset/` via CSS background-image
- Admin icons: Prefer `@wordpress/icons`, fallback to `lucide-react`
- If frontend shows SVG = BUG (check IconRegistry.php)

## 📋 Workflow: Task-Based Development

**Phase 1 = 33 atomic tasks**. Each task = one commit.

```bash
# 1. Pick next task from phase1-rewrite-foundation.instructions.md
# 2. Mark "IN PROGRESS" in PROGRESS-TRACKER.md
# 3. Write tests FIRST (TDD)
# 4. Implement per checklist
# 5. Run tests, verify pass
# 6. Update PROGRESS-TRACKER.md (date, commit hash)
# 7. Update CHANGELOG.md
# 8. Commit: [PHASE1-XXX] Description
```

## 🔄 Git Operations (GitHub MCP Tools ONLY)

**NEVER use terminal git commands** (`git commit`, `git push`, etc.).

Use MCP tools:
- `mcp_gitkraken_git_add_or_commit` - Stage & commit
- `mcp_gitkraken_git_push` - Push
- `mcp_gitkraken_git_status` - Check status
- `mcp_gitkraken_git_branch` - Create/list branches

**Commit format**: `[PHASE1-XXX] Brief description`

## 🧪 Testing Philosophy (Test-First)

**Phase 1A (tasks 1-8) MUST be completed before writing ANY new code:**
1. Document current HTML output from archived code
2. Create visual regression baselines (Playwright screenshots)
3. Write unit tests capturing current behavior
4. Test all iconsets, placements, shortcodes

**Why**: New code must produce IDENTICAL output to v2.2.1

**Test types**:
- Unit (PHPUnit): Individual methods, pure logic
- Integration (PHPUnit): Database, WordPress hooks
- E2E (Playwright): Full user workflows
- Visual (Playwright): Pixel-perfect output matching

## 🎯 Code Standards

**PHP**:
- PSR-4: `HtmlSocialShare\` namespace
- PHP 5.6+ compatible (no typed properties, use docblocks)
- WordPress Coding Standards
- 80%+ test coverage

**JavaScript/React**:
- TypeScript preferred
- Tailwind CSS (utility-first, no custom CSS unless necessary)
- `@wordpress/components`, `@wordpress/i18n`, `@wordpress/api-fetch`
- Components: Small (<300 lines), single responsibility
- Decompose complex components into subdirectories (e.g., `tabs/networks/`)

## 📦 Dependencies

**Prefer WordPress packages first:**
- `@wordpress/components` - UI
- `@wordpress/icons` - Icons
- `@wordpress/i18n` - Translations
- `@wordpress/api-fetch` - REST calls

**Fallbacks:**
- `lucide-react` - Icons only when `@wordpress/icons` lacks it (tree-shakable)

**Never add:**
- Generic icon libraries (icons come from `assets/iconset/`)
- Heavy frameworks duplicating WordPress functionality

## 🛠️ Utility Scripts Pattern

For repetitive tasks, write focused Node.js scripts in `scripts/`:

```javascript
// Example: scripts/generate-iconset-manifest.js
// Scans assets/iconset/ and generates metadata

// Example: scripts/validate-icons.js  
// Checks all iconsets have required networks

// Example: scripts/migrate-options.js
// Tests options migration from v2.2.1 format
```

**Use for**: Bulk operations, validation, code generation, migrations

## 📚 Key Reference Files

**Understand current behavior (archived):**
- `archive/html-social-share.php` - Main plugin class, hooks
- `archive/iconsets.php` - Icon registry, CSS injection
- `archive/shortcode.php` - Shortcode handler
- `archive/widget.php` - Widget implementation
- `archive/iconset/*/ssb.php` - Iconset class definitions

**Phase 1 documentation (your blueprint):**
- `.github/prompts/phase1-rewrite-foundation.prompt.md` - 33 tasks with checklists
- `documentation/architecture-diagrams.md` - Visual before/after
- `documentation/iconset-system-reference.md` - Icon system deep-dive
- `documentation/archive2-analysis.md` - What NOT to do (failed attempt)

**Options structure** (must maintain compatibility):
```php
// Current v2.2.1 flat structure (DO NOT CHANGE)
array(
  'title' => 'Share this...',
  'iconset' => 'default',
  'show_in' => array('show_left' => 'square', ...),
  'icons' => array('facebook' => '1', ...)
)
```

## 🚨 Critical Rules

**MUST DO:**
- Write tests before implementation (TDD)
- Support PHP 5.6-8.5+
- Use `pnpm`, not npm
- Use `wp-env` for testing
- Use GitHub MCP tools for git
- Maintain identical frontend HTML/CSS output
- Commit atomically (1 task = 1 commit)

**MUST NOT:**
- Skip Phase 1A tests (tasks 1-8)
- Change frontend output format
- Use inline SVG for frontend icons
- Mix archived code with new code
- Use terminal git commands
- Add heavy dependencies without approval

## 💬 When to Ask

**Ask maintainer:**
- Need to change `hssAdminConfig` localized keys (breaks admin UI)
- Want to add third-party dependency (document size/reason)
- Unclear task dependencies or success criteria

**Debug first:**
- Read error messages fully
- Check related test files
- Consult `.github/prompts/archive2-analysis.md` for anti-patterns
- Review similar implementations in `archive/`

## 📝 Documentation Requirements

**Update on every commit:**
1. `CHANGELOG.md` - What changed, why
2. `PROGRESS-TRACKER.md` - Task status, date, commit hash
3. PHPDoc/JSDoc - Inline code documentation
4. Test comments - Assertions and edge cases

---

**Remember**: This is a complete rewrite maintaining 100% backward compatibility. The archived v2.2.1 code still works - your job is to rebuild it with modern architecture while producing identical output. Read the Phase 1 plan, test first, commit atomically. 🚀

---

*Last Updated: October 8, 2025*  
*Phase 1 Rewrite - Fresh Start*  
*WordPress Plugin: HTML Social Share Buttons v3.0.0*
