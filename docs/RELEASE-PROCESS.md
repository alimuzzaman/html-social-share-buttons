# Release process

Use this process for every public plugin release.

## Rules

- Release only from `master`. Never create a release tag from a feature branch.
- Keep the release commit and the reviewed archive immutable after validation.
- A tag push runs validation only. It does not publish to WordPress.org.
- Publication requires an explicit manual workflow dispatch with the exact
  reviewed archive SHA-256 and confirmation `publish-vX.Y.Z`.
- The licensed WPBakery editor-picker E2E is intentionally skipped. The
  stored-shortcode, mapping, bundle, and public-render contracts remain the
  supported WPBakery coverage.

## Steps

1. Merge the reviewed release commit into `master` and ensure the worktree is
   clean.
2. Run the local release checks and build the production archive with Composer
   development dependencies removed:

   ```sh
   composer install --no-dev --prefer-dist --no-interaction --classmap-authoritative
   HSSB_ARCHIVE_PATH=/tmp/html-social-share-buttons.X.Y.Z.zip pnpm run zip
   shasum -a 256 /tmp/html-social-share-buttons.X.Y.Z.zip
   ```

3. Restore development dependencies for local work, then push `master`.
4. Create and push an annotated tag from the exact `master` commit:

   ```sh
   git tag -a vX.Y.Z -m "Release X.Y.Z" HEAD
   git push origin master
   git push origin refs/tags/vX.Y.Z
   ```

5. Wait for the tag-triggered `Validate and manually publish to
   WordPress.org` workflow. Continue only when every required validation job is
   green. A failed validation must be fixed in a new reviewed commit; do not
   bypass the gate.
6. Dispatch the same workflow on the tag with:
   - `reviewed_sha256`: the SHA-256 from the manually reviewed archive;
   - `confirmation`: exactly `publish-X.Y.Z`.
7. Confirm the publication job succeeds and record the workflow URL, source
   commit, tag, archive SHA-256, and WordPress.org availability.

## Recovery

If a tag was pushed from the wrong branch or with the wrong bytes and no
publication occurred, stop publication, correct `master`, and obtain a fresh
reviewed archive and tag. Do not dispatch publication for the invalid tag.
