# Unit Testing WordPress Plugins

## Overview

Unit tests verify individual pieces of your plugin—functions or classes—in isolation, ensuring changes don’t introduce regressions. They run faster than loading the full WordPress stack and catch logic errors before they affect live sites. When tests load WordPress core they become integration tests, so aim to mock dependencies for pure unit testing.

---

## 1. Install PHPUnit

- Check your PHP version and WordPress compatibility to pick the right PHPUnit release (see “PHPUnit Compatibility and WordPress Versions” in the official docs).
- Install via Composer globally or as a dev dependency:
  ```bash
  composer require --dev phpunit/phpunit
  ```

---

## 2. Scaffold Your Plugin Tests

- Use WP-CLI to generate all necessary files and folders in one command:
  ```bash
  wp scaffold plugin-tests my-plugin
  ```
- This creates:
  - A `tests/` directory for your test cases
  - `phpunit.xml.dist` for PHPUnit configuration
  - `.travis.yml` and `phpcs.ruleset.xml` for CI and coding standards.

---

## 3. Set Up the Local Testing Environment

- Run the installer script to spin up a temporary WordPress install and test database:
  ```bash
  bash build/bin/install-wp-tests.sh wordpress_test root '' localhost latest
  ```
  - `wordpress_test`: test database name (all data removed on each run)
  - `root` and `''`: MySQL user and password
  - `localhost`: database host
  - `latest`: WordPress version to download
- The script installs WordPress under `/tmp` by default and sets up the WP test framework.

---

## 4. Write Pure Unit Tests

Unit tests focus on your code, not WordPress internals. Mock or stub any WP functions or global state to isolate logic.

```php
<?php
class DisableRestFieldsTest extends WP_UnitTestCase {
    public function test_disable_default_rest_fields() {
        $endpoints = [
            '/wp/v2/users'        => [],
            '/wp/v2/users/123'    => [],
            '/wp/v2/users/me'     => [],
            '/wp/v2/media'        => [],
            '/wp/v2/media/456'    => [],
        ];

        $service = new My_Service();
        $filtered = $service->disableDefaultRestFields($endpoints);

        // Assert that each disabled route is removed
        foreach ([
            '/wp/v2/users',
            '/wp/v2/users/(?P<id>[\d]+)',
            '/wp/v2/users/me',
            '/wp/v2/media',
            '/wp/v2/media/(?P<id>[\d]+)',
        ] as $route) {
            $this->assertArrayNotHasKey($route, $filtered);
        }
    }
}
```
This test exercises only your `disableDefaultRestFields()` method without loading other WP endpoints or actions.

---

## 5. Run Your Tests

- Locally, simply execute:
  ```bash
  phpunit
  ```
- For continuous integration, commit your code to GitHub and enable Travis CI. The scaffolded `.travis.yml` will automatically run your suite on each push.

---

## Best Practices

- Write small, single-responsibility methods to simplify testing.
- Use mocks or stubs (with PHPUnit’s mocking API) for any external calls or globals.
- Name tests clearly: `test_methodName_expectedBehavior`.
- Keep test data minimal and reset state in `setUp()`/`tearDown()` hooks.
- Aim for high coverage on business logic; integration tests can cover end-to-end flows.

---

## Next Steps

- Explore integration testing with [WPBrowser](https://wpbrowser.wptestkit.dev/) for UI and API flows.
- Measure coverage with Xdebug and generate reports via Coveralls or Codecov.
- Automate code style checks with PHPCS and static analysis with PHPStan in your CI pipeline.