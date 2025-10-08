# BetterLinks Integration

## Overview

Integrate with the BetterLinks WordPress plugin to provide link shortening and tracking capabilities for social share URLs.

## Current Issue

Social share URLs are plain links without shortening or tracking, missing opportunities for analytics and link management.

## Implementation Approach

Detect BetterLinks plugin presence and automatically shorten/tracking social share URLs when available.

## Available Hooks (From Source Code Analysis)

### Free Version Hooks

- `betterlinks/pre_before_redirect` (filter) - Before redirect validation
- `betterlinks/link/before_dispatch_redirect` (filter) - Before redirect dispatch
- `betterlinks/before_redirect` (action) - Before redirect execution
- `betterlinks/api/params` (filter) - Link creation parameters
- `betterlinks/api/links_create_item_permissions_check` (filter) - Create permissions

### Pro Version Hooks

- `betterlinks/link/get_link_by_slug` (filter) - Get link by slug
- `betterlinks/pre_before_redirect` (action) - Pre redirect action
- `betterlinks/before_redirect` (action) - Before redirect
- `betterlinks/link/before_dispatch_redirect` (filter) - Before dispatch
- `betterlinks/link/target_url` (filter) - Target URL modification
- `betterlinks/site_url` (filter) - Site URL modification
- `betterlinks_before_cle` (filter) - Before Create Link Externally
- `betterlinks/link/before_start_tracking` (action) - Before tracking
- `betterlinks/link/after_insert_click` (action) - After click insertion
- `betterlinks/link/insert_click_arg` (filter) - Click insertion args
- `betterlinks/make_cloaked_redirect` (action) - Cloaked redirect
- `btl_header_redirect` (action) - Header redirect

## Implementation Details

### URL Shortening

- Use `\BetterLinks\Helper::insert_link()` to create short links
- Generate random slug with `\BetterLinks\Helper::generate_random_slug()`
- Get full short URL with `\BetterLinks\Helper::generate_short_url()`
- Apply `betterlinks/api/params` filter during creation

### Tracking Integration

- BetterLinks has built-in click tracking
- Add UTM parameters to target URLs for additional analytics
- Hook into `betterlinks/link/after_insert_click` for custom tracking
- Use `betterlinks/link/insert_click_arg` filter for tracking data

### Detection

- Check for `class_exists('BetterLinks')`
- Verify API availability by checking helper functions exist

## Tasks

### BETTERLINKS-001: Detect BetterLinks Plugin (2 hours)

**Objective**: Check for BetterLinks availability

**Details**: Implement plugin detection, version checking, API availability

**Success Criteria**: Correctly detects BetterLinks presence and capabilities

**Status**: ✅ Completed - Added BetterLinksIntegration::isAvailable() method

### BETTERLINKS-002: Implement URL Shortening (3 hours)

**Objective**: Shorten share URLs using BetterLinks

**Details**: Hook into share URL generation, apply BetterLinks shortening

**Success Criteria**: Share URLs are shortened when BetterLinks active

**Status**: ✅ Completed - Added BetterLinksUrlFilter and hss_share_url filter

### BETTERLINKS-003: Add Tracking Parameters (3 hours)

**Objective**: Add UTM parameters for analytics

**Details**: Configure tracking parameters, integrate with BetterLinks analytics

**Success Criteria**: Share links include proper tracking

**Status**: ✅ Completed - Added UTM parameters in BetterLinksUrlFilter

### BETTERLINKS-004: Admin Integration (2 hours)

**Objective**: Add BetterLinks settings to admin

**Details**: Enable/disable integration, configure tracking options

**Success Criteria**: Admin can control BetterLinks integration

**Status**: ✅ Completed - Added settings to General tab in admin

### BETTERLINKS-005: Testing and Compatibility (3 hours)

**Objective**: Test integration thoroughly

**Details**: Test with different BetterLinks versions, error handling

**Success Criteria**: Integration works reliably

**Status**: ✅ Completed - Basic testing passed, error handling implemented

**Objective**: Test integration thoroughly

**Details**: Test with different BetterLinks versions, error handling

**Success Criteria**: Integration works reliably

## Dependencies

- Requires BetterLinks plugin
- Requires share URL generation system (completed)

## Benefits

- Improved link management
- Better analytics tracking
- Professional URL shortening
- Enhanced user experience</content>

