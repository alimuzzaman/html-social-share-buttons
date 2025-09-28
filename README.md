html-social-share-buttons

Quick setup

1. Install PHP dependencies

- Ensure PHP 7.4+ is installed and one of the supported image extensions (GD or Imagick) is available if you plan to use local QR generation.
- From the project root run:

composer install

For a production install:

composer install --no-dev --optimize-autoloader

2. Install JS dependencies (optional for block editor/E2E tests)

- The repo uses Playwright and node tools. Use either npm or pnpm depending on your workflow:

npm install
# or
pnpm install

3. Running tests

- Unit tests (PHPUnit) require the WordPress PHPUnit test suite to be provisioned. See phpunit.xml.dist and tests/ for configuration, or run tests in CI.
- E2E tests use Playwright and are configured in the Playwright workflow; they are set to manual dispatch by default.

Notes

- The `vendor/` directory is intentionally not tracked in the repository. Keep `composer.json` and `composer.lock` committed.
- To reproduce the previously committed vendor state, run `composer install` locally.
- CI workflows are configured to require manual dispatch; they won't run automatically on PRs or pushes unless manually started.
