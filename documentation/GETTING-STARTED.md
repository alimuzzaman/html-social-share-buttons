# 🚀 Phase 1 Implementation - Getting Started Guide

## ✅ Prerequisites Checklist


Before starting PHASE1-001, ensure you have:

### Code Quality Tools

- [ ] **PHP_CodeSniffer** for WordPress standards
    ```bash
    composer global require "squizlabs/php_codesniffer=*"
    composer global require "wp-coding-standards/wpcs"
    ```

### Required Software

### Required Software
- [ ] **PHP 5.6 - 8.5+** installed (broad compatibility)
    ```bash
    php -v  # Should show 5.6 or higher
    ```

- [ ] **Composer** installed
  ```bash
  composer --version
  ```

- [ ] **Node.js 18+** and pnpm
  ```bash
  node -v  # Should show v18 or higher
  pnpm -v  # Install: npm install -g pnpm
  ```

- [ ] **Git** installed
  ```bash
  git --version
  ```

### WordPress Test Environment

- [ ] **wp-env** (Required)
  ```bash

Start WordPress:
```bash
wp-env start
```

- [ ] **PHPUnit** for unit tests
  ```bash
  composer global require phpunit/phpunit
  ```

## 📋 Initial Setup Steps

### Step 1: Prepare Your Repository

```bash
# Ensure you're on the correct branch
cd /Users/alim/Sites/git/html-social-share-buttons
git status

# Create a Phase 1 development branch
git checkout -b phase1/foundation

# Verify current code is working
# Test the plugin in your WordPress environment
```

### Step 2: Initialize Composer

Create `composer.json` in the root:

```json
{
    "name": "alimuzzaman/html-social-share-buttons",
    "description": "Lightweight social share buttons for WordPress",
    "type": "wordpress-plugin",
    "license": "GPL-2.0-or-later",
    "autoload": {
        "psr-4": {
            "HtmlSocialShare\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "HtmlSocialShare\\Tests\\": "tests/"
        }
    },
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "wp-coding-standards/wpcs": "^3.0",
        "phpstan/phpstan": "^1.10",
        "yoast/phpunit-polyfills": "^2.0"
    },
    "scripts": {
        "test": "phpunit",
        "phpcs": "phpcs",
        "phpcbf": "phpcbf",
        "phpstan": "phpstan analyse"
    }
}
```

Install dependencies:
```bash
composer install
```

### Step 3: Initialize npm/Node

Create `package.json` in the root:

```json
{
  "name": "html-social-share-buttons",
  "version": "3.0.0",
  "description": "Lightweight social share buttons for WordPress",
  "scripts": {
    "test": "playwright test",
    "test:visual": "playwright test --project=chromium",
    "test:update": "playwright test --update-snapshots",
    "wp-env": "wp-env"
  },
  "devDependencies": {
    "@playwright/test": "^1.40.0",
    "@wordpress/env": "^9.0.0"
  }
}
```

Install dependencies:
```bash
npm install
```

### Step 4: Set Up wp-env

Create `.wp-env.json`:

```json
{
    "core": "WordPress/WordPress#6.4",
    "phpVersion": "5.6",
  "plugins": [
    "."
  ],
  "themes": [
    "WordPress/twentytwentyfour"
  ],
  "port": 8888,
  "testsPort": 8889,
  "config": {
    "WP_DEBUG": true,
    "WP_DEBUG_LOG": true,
    "SCRIPT_DEBUG": true
  },
  "mappings": {
    "wp-content/plugins/html-social-share-buttons": "."
  }
}
```

Start WordPress:
```bash
npx wp-env start
```

Access:
- Frontend: http://localhost:8888
- Admin: http://localhost:8888/wp-admin
  - User: admin
  - Pass: password

### Step 5: Set Up PHPUnit

Create `phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="tests/bootstrap.php"
    backupGlobals="false"
    colors="true"
    convertErrorsToExceptions="true"
    convertNoticesToExceptions="true"
    convertWarningsToExceptions="true"
    verbose="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    <coverage>
        <include>
            <directory>src</directory>
        </include>
        <report>
            <html outputDirectory="coverage/html"/>
            <clover outputFile="coverage/clover.xml"/>
        </report>
    </coverage>
    <php>
        <const name="WP_TESTS_PHPUNIT_POLYFILLS_PATH" value="vendor/yoast/phpunit-polyfills"/>
    </php>
</phpunit>
```

### Step 6: Set Up Playwright

Create `playwright.config.ts`:

```typescript
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/visual',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: 'http://localhost:8888',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
    {
      name: 'Mobile Chrome',
      use: { ...devices['Pixel 5'] },
    },
  ],
  webServer: {
    command: 'wp-env start',
    url: 'http://localhost:8888',
    reuseExistingServer: !process.env.CI,
  },
});
```

### Step 7: Create Directory Structure

```bash
# Create new directories
mkdir -p src/{Admin,Frontend,Renderers,Services,Build,Compatibility}
mkdir -p tests/{Unit,Integration,visual,fixtures}
mkdir -p build/iconset
mkdir -p assets/iconset

# Create placeholder files
touch tests/bootstrap.php
touch tests/fixtures/expected-output.php
```

### Step 8: Create Bootstrap File

Create `tests/bootstrap.php`:

```php
<?php
/**
 * PHPUnit bootstrap file for HTML Social Share Buttons
 */

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// WordPress tests directory
$_tests_dir = getenv('WP_TESTS_DIR');

if (!$_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file
if (!defined('WP_TESTS_PHPUNIT_POLYFILLS_PATH')) {
    define(
        'WP_TESTS_PHPUNIT_POLYFILLS_PATH',
        dirname(__DIR__) . '/vendor/yoast/phpunit-polyfills'
    );
}

// Give access to tests_add_filter()
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested
 */
function _manually_load_plugin() {
    require dirname(__DIR__) . '/html-social-share.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment
require $_tests_dir . '/includes/bootstrap.php';
```

## 🎯 PHASE1-001: Verify Setup

Now verify everything is working:

### 1. Test PHP
```bash
php -v
composer --version
```

### 2. Test WordPress Environment
```bash
npx wp-env start
curl http://localhost:8888
```

### 3. Test PHPUnit
```bash
# Install WordPress test suite
./tests/bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

Create this script at `tests/bin/install-wp-tests.sh`:

```bash
#!/usr/bin/env bash

if [ $# -lt 3 ]; then
    echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]"
    exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}
SKIP_DB_CREATE=${6-false}

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$TMPDIR/wordpress/}

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+\-(beta|RC)[0-9]+$ ]]; then
    WP_BRANCH=${WP_VERSION%\-*}
    WP_TESTS_TAG="branches/$WP_BRANCH"
elif [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+$ ]]; then
    WP_TESTS_TAG="branches/$WP_VERSION"
elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0-9]+ ]]; then
    if [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0] ]]; then
        WP_TESTS_TAG="tags/${WP_VERSION%??}"
    else
        WP_TESTS_TAG="tags/$WP_VERSION"
    fi
elif [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
    WP_TESTS_TAG="trunk"
else
    WP_TESTS_TAG="tags/$WP_VERSION"
fi

set -ex

install_wp() {
    if [ -d $WP_CORE_DIR ]; then
        return;
    fi

    mkdir -p $WP_CORE_DIR

    if [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
        mkdir -p $TMPDIR/wordpress-nightly
        download https://wordpress.org/nightly-builds/wordpress-latest.zip  $TMPDIR/wordpress-nightly/wordpress-nightly.zip
        unzip -q $TMPDIR/wordpress-nightly/wordpress-nightly.zip -d $TMPDIR/wordpress-nightly/
        mv $TMPDIR/wordpress-nightly/wordpress/* $WP_CORE_DIR
    else
        if [ $WP_VERSION == 'latest' ]; then
            local ARCHIVE_NAME='latest'
        elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+ ]]; then
            local ARCHIVE_NAME="wordpress-$WP_VERSION"
        else
            local ARCHIVE_NAME="wordpress-${WP_VERSION%??}"
        fi
        download https://wordpress.org/${ARCHIVE_NAME}.tar.gz  $TMPDIR/wordpress.tar.gz
        tar --strip-components=1 -zxmf $TMPDIR/wordpress.tar.gz -C $WP_CORE_DIR
    fi

    download https://raw.github.com/markoheijnen/wp-mysqli/master/db.php $WP_CORE_DIR/wp-content/db.php
}

install_test_suite() {
    if [ -d $WP_TESTS_DIR ]; then
        return;
    fi

    mkdir -p $WP_TESTS_DIR
    svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ $WP_TESTS_DIR/includes
    svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ $WP_TESTS_DIR/data
}

install_db() {
    if [ ${SKIP_DB_CREATE} = "true" ]; then
        return 0
    fi

    # parse DB_HOST for port or socket references
    local PARTS=(${DB_HOST//\:/ })
    local DB_HOSTNAME=${PARTS[0]};
    local DB_SOCK_OR_PORT=${PARTS[1]};
    local EXTRA=""

    if ! [ -z $DB_HOSTNAME ] ; then
        if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
            EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
        elif ! [ -z $DB_SOCK_OR_PORT ] ; then
            EXTRA=" --socket=$DB_SOCK_OR_PORT"
        elif ! [ -z $DB_HOSTNAME ] ; then
            EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
        fi
    fi

    # create database
    mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA
}

configure_wordpress() {
    if [ ! -f wp-config.php ]; then
        download https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/templates/wp-config.php "$WP_CORE_DIR"/wp-config.php
        sed -i "s/database_name_here/$DB_NAME/" "$WP_CORE_DIR"/wp-config.php
        sed -i "s/username_here/$DB_USER/" "$WP_CORE_DIR"/wp-config.php
        sed -i "s/password_here/$DB_PASS/" "$WP_CORE_DIR"/wp-config.php
        sed -i "s|localhost|${DB_HOST}|" "$WP_CORE_DIR"/wp-config.php
    fi
}

install_wp
install_test_suite
install_db
configure_wordpress
```

Make it executable:
```bash
chmod +x tests/bin/install-wp-tests.sh
```

### 4. Run Initial Test
```bash
composer test
```

### 5. Test Playwright
```bash
npm test
```

## ✅ Verification Checklist

Before proceeding to PHASE1-002:

- [ ] PHP 8.0+ installed and working
- [ ] Composer dependencies installed
- [ ] npm dependencies installed
- [ ] wp-env running (http://localhost:8888 accessible)
- [ ] Can access WordPress admin
- [ ] PHPUnit configured and running
- [ ] Playwright configured
- [ ] Directory structure created
- [ ] All prerequisite tools working

## 🎯 What's Next?

Once setup is verified, proceed to:

**PHASE1-002: Document Current HTML Output Patterns**

This task involves:
1. Manually testing all placement variations
2. Capturing HTML output
3. Capturing CSS output
4. Creating fixtures
5. Documenting patterns

See `.github/prompts/phase1-rewrite-foundation.prompt.md` for details.

## 🚨 Common Issues & Solutions

### Issue: wp-env won't start
```bash
# Clean up and restart
wp-env stop
wp-env clean all
wp-env start
```

### Issue: PHPUnit can't find WordPress tests
```bash
# Reinstall WordPress test suite
rm -rf /tmp/wordpress-tests-lib
./tests/bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

### Issue: Composer dependencies conflict
```bash
# Clear cache and reinstall
composer clear-cache
rm -rf vendor composer.lock
composer install
```

### Issue: Port 8888 already in use
Edit `.wp-env.json`:
```json
{
  "port": 9999,
  "testsPort": 9998
}
```

## 📚 Reference Commands

```bash
# Start WordPress
wp-env start

# Stop WordPress
wp-env stop

# Run PHPUnit tests
composer test

# Run specific test
composer test tests/Unit/SpecificTest.php

# Run Playwright tests
npm test

# Update visual baselines
npm run test:update

# Check code standards
composer phpcs

# Fix code standards
composer phpcbf

# Run static analysis
composer phpstan
```

## 🎉 Ready to Start!

You're now ready to begin Phase 1 implementation!

**Next:** Open `.github/prompts/phase1-rewrite-foundation.prompt.md` and start with PHASE1-001.

Good luck! 🚀
