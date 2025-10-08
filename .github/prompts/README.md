# HTML Social Share Buttons - Rewrite Plan

## 📁 Prompt Files

### Current Files

1. **`phase1-rewrite-foundation.instructions.md`** (Main Plan)
   - Complete ground-up rewrite strategy
   - 33 detailed tasks across 4 sub-phases
   - 150-190 hour estimate
   - Test-first approach
   - 100% backward compatibility

## 🎯 Quick Start

### What Phase 1 Achieves

Phase 1 rewrites the plugin from scratch while:
- ✅ Maintaining **identical frontend output** (HTML/CSS)
- ✅ Using **modern PSR-4 architecture**
- ✅ Adding **comprehensive test coverage**
- ✅ Unifying **all iconsets** (no legacy distinction)
- ✅ Keeping **flat options structure** (simple, not tab-based)
- ✅ Supporting **new iconset build system**

### What Phase 1 Does NOT Include

Phase 1 focuses on foundation only:
- ❌ No Tailwind admin UI (comes in Phase 2)
- ❌ No new networks (comes in Phase 3)
- ❌ No new features (comes in Phase 3+)
- ❌ No React admin (comes in Phase 2)

## 📊 Project Structure

### Phase Breakdown

```
Phase 1: Foundation (150-190 hrs) ← YOU ARE HERE
├── 1A: Test Infrastructure (40-50 hrs)
├── 1B: Core Architecture (50-60 hrs)
├── 1C: Iconset Build System (30-40 hrs)
└── 1D: Integration & Testing (30-40 hrs)

Phase 2: Modern Admin UI (TBD)
├── Tailwind CSS
├── React components
└── Enhanced UX

Phase 3: New Features (TBD)
├── New networks
├── Share counts
└── Analytics

Phase 4: Advanced (TBD)
└── Premium features
```

## 🎓 Key Concepts from Analysis

### Current System Understanding

**How Icons Render Now:**
```
PNG Image → CSS background-image → <a> element
```

**HTML Structure:**
```html
<div class="zmshbt {iconset} {type}">
  <a class="{network}" href="{shareUrl}"></a>
</div>
```

**CSS Injection:**
```css
.zmshbt.default.square .facebook {
  background-image: url('path/to/facebook.png');
}
```

### New System (Phase 1)

**Architecture:**
```
IconRegistry → UrlBuilder → ButtonRenderer → HTML Output
                              ↓
                          CssGenerator → CSS Output
```

**Iconset Structure:**
```
assets/iconset/
  default_square/
    *.png
    style.css (generated)
  flat_circle/
    *.png
    style.css (generated)
```

**Build Process:**
```
IconsetBuilder scans assets/iconset/
  → Generates CSS
  → Outputs to build/iconset/{name}.css
```

## 🚀 How to Use This Plan

### For AI Coding Agents

Reference the prompt file:
```
#file:.github/prompts/phase1-rewrite-foundation.instructions.md

Please implement task PHASE1-XXX
```

### For Human Developers

1. Read the main plan: `phase1-rewrite-foundation.instructions.md`
2. Start with Phase 1A (tests first!)
3. Follow tasks in order
4. Check off tasks as you complete them
5. Update status in the task list

### Task Status Tracking

Each task has a status indicator:
- ❌ NOT STARTED
- ⏳ IN PROGRESS  
- ✅ COMPLETED
- ⚠️ BLOCKED

Update these as you work!

## 📋 Task Overview

### Phase 1A: Test Infrastructure (8 tasks)
Tests capture current behavior before any changes

### Phase 1B: Core Architecture (11 tasks)  
Build new PSR-4 classes that replicate current functionality

### Phase 1C: Iconset Build System (7 tasks)
Migrate iconsets to new structure and build system

### Phase 1D: Integration & Testing (7 tasks)
Wire everything together and verify parity

## ⚠️ Critical Rules

### The Golden Rules

1. **Write Tests First**: Always test before implementing
2. **No Visual Changes**: Frontend must look identical
3. **Backward Compatible**: Don't break existing sites
4. **Keep It Simple**: Phase 1 is foundation only
5. **Document Everything**: Every class needs docs

### What Makes This Different from archive2?

| Aspect | archive2 | Phase 1 New Plan |
|--------|----------|------------------|
| Options | Tab-based, complex | Flat, simple, organized |
| Iconsets | Legacy vs Modern | All unified, same method |
| Admin UI | Complex tabs | Keep simple for now |
| Scope | Too ambitious | Foundation first |

## 📚 Key Documents to Reference

### Must Read
- `docs/options.md` - Current options structure
- `phase1-rewrite-foundation.instructions.md` - Main plan

### Background Reading
- `archive2/docs/02-Current-State-Analysis.md` - What exists now
- `archive2/docs/05-Frontend-Icon-System-Overview.md` - Icon concepts
- `archive2/docs/08-Server-Rendered-Share-Buttons.md` - Rendering concepts

### Reference (What to Avoid)
- archive2 tab-based settings
- archive2 profile system (not needed yet)
- archive2 legacy/modern split (we're unifying)

## 🎯 Success Criteria

Phase 1 is **COMPLETE** when:

1. ✅ All 33 tasks marked COMPLETED
2. ✅ All tests pass (unit + visual)
3. ✅ Frontend output is pixel-perfect identical
4. ✅ Performance is equal or better
5. ✅ Migration script works flawlessly
6. ✅ Documentation is complete
7. ✅ No breaking changes for users

## 🤔 Decision Log

### Why Test-First Approach?
- Captures current behavior as requirements
- Prevents regressions
- Enables fearless refactoring

### Why PSR-4?
- Modern PHP standard
- Better organization
- Easier testing
- Reduced conflicts

### Why Unified Iconsets?
- Simpler mental model
- Less code duplication
- Easier to extend
- Better maintainability

### Why Flat Options?
- Matches current structure
- Easier migration
- Less complexity
- Can reorganize in Phase 2

## 📞 Getting Help

### Stuck on a Task?

1. Review the task's Implementation checklist
2. Check reference materials
3. Look at similar completed tasks
4. Ask for clarification with context

### Found an Issue?

1. Document the issue
2. Check if it affects other tasks
3. Update task status to ⚠️ BLOCKED
4. Note the blocker in task description

## 🎉 Next Steps

1. **Read the main plan**: Start with `phase1-rewrite-foundation.instructions.md`
2. **Set up environment**: Follow prerequisites
3. **Start with PHASE1-001**: Test environment setup
4. **Work sequentially**: Each task builds on previous ones
5. **Track progress**: Update task statuses
6. **Commit atomically**: One task = one commit

---

**Remember**: Phase 1 is about building a solid foundation with zero regressions. Speed comes later. Quality comes first.

Good luck! 🚀
