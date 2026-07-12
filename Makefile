.PHONY: build watch frontend-capture frontend-compare frontend-drift-surface admin-react-smoke settings-sanitize-contract share-template-contract exclude-contract current-url-contract phpunit settings-local-checks help check-wp-root

WP_ROOT ?= $(shell [ -n "$(WP_ROOT)" ] && echo "$(WP_ROOT)" || echo "")

check-wp-root:
	@if [ -z "$(WP_ROOT)" ]; then \
		echo "WP_ROOT is required. Example: WP_ROOT=/var/www/html make frontend-capture"; \
		exit 1; \
	fi

help:
	@echo "Targets:"
	@echo "  build               Build the WordPress admin bundle"
	@echo "  watch               Watch and rebuild the WordPress admin bundle"
	@echo "  frontend-capture   Capture frontend output baseline fixtures"
	@echo "  frontend-compare   Compare current output against baseline"
	@echo "  frontend-drift-surface   Verify frontend renderer files are unchanged"
	@echo "  admin-react-smoke  Verify React settings render and legacy field names"
	@echo "  settings-sanitize-contract  Verify saved settings keep legacy shape"
	@echo "  share-template-contract  Verify platform share URL templates"
	@echo "  exclude-contract  Verify excluded post identifiers"
	@echo "  current-url-contract  Verify share links use the configured WordPress URL"
	@echo "  phpunit             Run the Composer PHPUnit contract suite"
	@echo "  settings:check       Run local settings checks via pnpm"

build:
	@pnpm run build

watch:
	@pnpm run start

frontend-capture: check-wp-root
	@php tests/frontend-output-regression.php capture \
		--wp-root=$(WP_ROOT) \
		--scenario-file=tests/frontend-output-scenarios.json \
		--output=tests/fixtures/frontend-output-baseline.json

frontend-compare: check-wp-root
	@php tests/frontend-output-regression.php compare \
		--wp-root=$(WP_ROOT) \
		--baseline=tests/fixtures/frontend-output-baseline.json \
		--scenario-file=tests/frontend-output-scenarios.json --strict

frontend-drift-surface:
	@bash tests/frontend-drift-surface.sh

admin-react-smoke:
	@node --check assets/admin-react.js
	@node tests/admin-react-smoke.js

settings-sanitize-contract:
	@php -l settings_page.php
	@php tests/settings-sanitize-contract.php

share-template-contract:
	@php -l share-templates.php
	@php tests/share-template-contract.php

exclude-contract:
	@php -l function.php
	@php tests/exclude-contract.php

current-url-contract:
	@php tests/current-url-contract.php

phpunit:
	@composer test

settings-local-checks:
	@pnpm run settings:check
