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

## Running PHPUnit locally (WordPress test suite)

To run the PHPUnit tests locally you'll need to provision the WordPress PHPUnit test suite (`wordpress-tests-lib`) and a test database. This repository includes a helper script `bin/install-wp-tests.sh` which automates the setup.

1. Install PHP CLI, Composer and MySQL locally.
2. From the project root run:

    # Create a database for tests (adjust names, user and password)
    bin/install-wp-tests.sh hss_test_db hss_user secret_password localhost latest

This will download WordPress core and the `wordpress-tests-lib` into a temporary location and create `wp-tests-config.php` pointing at the test database.

3. Install composer dependencies (if not already):

    composer install

4. Run PHPUnit:

    vendor/bin/phpunit --configuration phpunit.xml.dist

Notes:
- The `bin/install-wp-tests.sh` script will attempt to recreate or create the test database and requires MySQL command-line access.
- Some unit tests in this codebase are pure-PHP and do not require WordPress globals; others rely on `wordpress-tests-lib` and will fail until the test suite is provisioned.
- CI workflows in `.github/workflows` are configured to run tests in a controlled environment; they are set to manual dispatch by default.

Notes

- The `vendor/` directory is intentionally not tracked in the repository. Keep `composer.json` and `composer.lock` committed.
- To reproduce the previously committed vendor state, run `composer install` locally.
- CI workflows are configured to require manual dispatch; they won't run automatically on PRs or pushes unless manually started.
