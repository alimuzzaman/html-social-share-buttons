.PHONY: build watch zip frontend-capture frontend-compare frontend-drift-surface admin-react-smoke share-template-contract exclude-contract settings-local-checks help check-wp-root

WP_ROOT ?= $(shell [ -n "$(WP_ROOT)" ] && echo "$(WP_ROOT)" || echo "")

check-wp-root:
	@if [ -z "$(WP_ROOT)" ]; then \
		echo "WP_ROOT is required. Example: WP_ROOT=/var/www/html make frontend-capture"; \
		exit 1; \
	fi

help:
	@echo "Targets:"
	@echo "  build               Build the WordPress admin and block bundles"
	@echo "  watch               Watch and rebuild the WordPress admin and block bundles"
	@echo "  zip                Create the distribution archive in the project parent directory"
	@echo "  frontend-capture   Capture frontend output baseline fixtures"
	@echo "  frontend-compare   Compare current output against baseline"
	@echo "  frontend-drift-surface   Verify frontend renderer files are unchanged"
	@echo "  admin-react-smoke  Verify React settings render and legacy field names"
	@echo "  share-template-contract  Verify platform share URL templates"
	@echo "  exclude-contract  Verify excluded post identifiers"
	@echo "  settings:check       Run local settings checks via pnpm"

build:
	@pnpm run build

watch:
	@pnpm run start

zip:
	@pnpm run zip

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
	@php tests/frontend-drift-surface.php

admin-react-smoke:
	@node --check assets/admin-react.js
	@node tests/admin-react-smoke.js

share-template-contract:
	@php -l share-templates.php
	@php tests/share-template-contract.php

exclude-contract:
	@php -l function.php
	@php tests/exclude-contract.php

settings-local-checks:
	@pnpm run settings:check
