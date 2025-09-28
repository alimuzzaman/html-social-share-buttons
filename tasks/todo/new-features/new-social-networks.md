# New Social Networks Support

## Overview

Add support for emerging and missing social networks to stay competitive and meet user demands.

## Missing Networks Identified

Based on competitor analysis and user feedback:

- Mastodon
- Bluesky
- Threads (Meta)
- VK (VKontakte)
- WeChat
- Instagram Direct
- Messenger
- Telegram (already supported)
- Reddit (already supported)
- Tumblr (already supported)

## Tasks

### NEWNET-001: Add Mastodon Support (3 hours)

**Objective**: Implement Mastodon sharing and profile links

**Details**:

- Add Mastodon to networks registry
- Implement share URL template: `https://mastodon.social/share?text={title}%20{url}`
- Add Mastodon icon (SVG)
- Update profile URL handling for Mastodon instances
- Add to admin UI network selection

**Success Criteria**:

- Mastodon appears in network dropdown
- Share URLs work correctly
- Profile links work with custom instances

### NEWNET-002: Add Bluesky Support (3 hours)

**Objective**: Implement Bluesky sharing and profile links

**Details**:

- Add Bluesky to networks registry
- Implement share URL template: `https://bsky.app/intent/compose?text={title}%20{url}`
- Add Bluesky icon (SVG)
- Update profile URL handling
- Add to admin UI

**Success Criteria**:

- Bluesky sharing works
- Profile links functional

### NEWNET-003: Add Threads Support (3 hours)

**Objective**: Implement Meta Threads sharing and profile links

**Details**:

- Add Threads to networks registry
- Implement share URL template: `https://www.threads.net/intent/post?text={title}%20{url}`
- Add Threads icon (SVG)
- Update profile URL handling
- Add to admin UI

**Success Criteria**:

- Threads sharing functional
- Profile links work

### NEWNET-004: Add VK Support (3 hours)

**Objective**: Implement VKontakte sharing and profile links

**Details**:

- Add VK to networks registry
- Implement share URL template: `https://vk.com/share.php?url={url}&title={title}`
- Add VK icon (SVG)
- Update profile URL handling
- Add to admin UI

**Success Criteria**:

- VK sharing works
- Profile links functional

### NEWNET-005: Add WeChat Support (4 hours)

**Objective**: Implement WeChat sharing (note: WeChat doesn't support direct web sharing)

**Details**:

- Add WeChat to networks registry
- Implement QR code generation for sharing
- Add WeChat icon (SVG)
- Note: WeChat requires QR codes for web sharing
- Add to admin UI with appropriate messaging

**Success Criteria**:

- WeChat appears in networks
- QR code sharing implemented

## Dependencies

- Requires icon registry system (completed)
- Requires profile management system (completed)

## Testing Strategy

- Test share URLs on each platform
- Test profile links
- Verify icons display correctly
- Test admin UI integration
