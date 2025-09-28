# Data Model and Options Schema Implementation

## Overview

Implement the unified data model and migration plan defined in `docs/04-Data-Model-and-Options-Schema.md`. The current Settings class uses in-memory storage only and doesn't persist to WordPress options. Need to implement the new schema with `hss_core`, `hss_profiles`, and `hss_icons` options.

## Current Issue

- Settings are stored in memory only, not persisted to database
- Old legacy option structure still being used
- No migration from legacy to new schema implemented
- Icon registry not implemented according to new schema

## Tasks

### DATAMODEL-001: Implement New Options Schema Persistence (4 hours)

**Objective**: Update Settings class to persist data using new hss_core/hss_profiles/hss_icons schema

**Details**:
- Modify Settings class to use WordPress update_option/get_option
- Implement separate storage for hss_core, hss_profiles, hss_icons
- Add caching layer with proper invalidation
- Ensure backward compatibility during transition

**Success Criteria**:
- Settings persist across page loads
- Data stored in correct WordPress options
- Caching implemented and working

### DATAMODEL-002: Implement Legacy Options Migration (6 hours)

**Objective**: Create migration system to convert legacy options to new format

**Details**:
- Create migration runner function `hss_migrate_legacy_options()`
- Map legacy options to new schema (title, positions, networks, etc.)
- Create default profiles for enabled networks
- Implement migration flag and rollback capability
- Add admin migration status display

**Success Criteria**:
- Legacy options successfully migrated
- Migration is idempotent (can run multiple times safely)
- Admin shows migration status and results

### DATAMODEL-003: Create Icon Registry Structure (5 hours)

**Objective**: Implement hss_icons option structure for icon sets and custom icons

**Details**:
- Create icon registry with sets and custom icons structure
- Implement SVG sanitization for custom icons
- Add icon lookup and resolution logic
- Support builtin, set, and custom icon sources
- Implement icon reference system (source + ref tuple)

**Success Criteria**:
- Icon registry stores and retrieves icons correctly
- SVG sanitization working
- Icon lookup resolves correct SVG markup

### DATAMODEL-004: Update Profile Management (4 hours)

**Objective**: Update ProfileManager to work with hss_profiles option structure

**Details**:
- Modify ProfileManager to use hss_profiles option
- Implement profile CRUD operations
- Add profile validation and sanitization
- Support share vs profile types
- Implement URL template replacement

**Success Criteria**:
- Profiles load from and save to hss_profiles option
- Profile CRUD operations working
- URL templates properly replaced

### DATAMODEL-005: Update Admin Interface (5 hours)

**Objective**: Update admin settings page to use new schema

**Details**:
- Modify SettingsPage to work with new option structure
- Update form fields to match new schema
- Add migration status display
- Implement profile management UI
- Update icon picker to use new registry

**Success Criteria**:
- Admin interface saves to correct options
- Migration status visible to admin
- Profile management working

### DATAMODEL-006: Implement Options Caching (3 hours)

**Objective**: Add caching layer for options with proper invalidation

**Details**:
- Implement wp_cache for options loading
- Add cache invalidation on option updates
- Use transients for expensive operations
- Implement cache versioning

**Success Criteria**:
- Options load from cache when available
- Cache properly invalidated on updates
- Performance improved for repeated option access

### DATAMODEL-007: Add Migration and Persistence Tests (4 hours)

**Objective**: Create unit and integration tests for migration and persistence

**Details**:
- Test migration from legacy options
- Test option persistence and retrieval
- Test icon registry operations
- Test profile management
- Add integration tests for admin saving

**Success Criteria**:
- All tests pass
- Migration tested with various legacy data
- Persistence verified across requests

## Dependencies

- Requires PSR-4 autoloading (PHASE1-002)
- Requires basic Settings class structure (PHASE1-007)
- Should be implemented before admin UI enhancements

## Testing Strategy

- Unit tests for each component
- Integration tests for migration
- Manual testing of admin interface
- Performance testing for caching

## Rollback Plan

- Keep legacy options as backup during migration
- Provide migration rollback function
- Clear caches and transients on rollback

## Success Metrics

- Settings persist correctly across page loads
- Migration completes without data loss
- Admin interface works with new schema
- Performance meets requirements
- All tests pass