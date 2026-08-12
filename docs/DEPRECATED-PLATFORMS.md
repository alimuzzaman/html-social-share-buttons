# Retired and renamed sharing platforms

This is the current platform policy. Historical audit text that said these
changes were pending is obsolete.

## Removed networks

- **Google Plus** was discontinued in April 2019 and is not registered by the
  current plugin.
- **Google Bookmarks** was discontinued in September 2021 and is not registered
  by the current plugin.

Existing unknown settings keys are preserved by the compatibility codec where
required, but retired networks are not offered or rendered as built-in share
actions.

## Twitter to X compatibility

The current built-in network is **X** and its default template uses `x.com`.
Historical `twitter` values, CSS classes, filenames, and profile-setting inputs
remain compatibility aliases where removing them would break saved data,
extensions, or public markup. Storage/runtime mapping normalizes them to the
canonical X network without silently rewriting unrelated saved settings.

## Current built-in share actions

Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and Email.

Changing this list requires synchronized registry, settings, icon-manifest,
translation, storage, renderer, browser, and compatibility-contract updates.
