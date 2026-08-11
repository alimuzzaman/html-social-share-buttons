.PHONY: build watch zip frontend-capture frontend-compare admin-react-smoke share-template-contract exclude-contract settings-local-checks ajax-contracts multisite-contracts help check-wp-root

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
	@echo "  admin-react-smoke  Verify React settings render and legacy field names"
	@echo "  share-template-contract  Verify platform share URL templates"
	@echo "  exclude-contract  Verify excluded post identifiers"
	@echo "  settings:check       Run local settings checks via pnpm"
	@echo "  ajax-contracts       Run WordPress AJAX integration contracts"
	@echo "  multisite-contracts  Run WordPress multisite settings contracts"

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

admin-react-smoke:
	@pnpm run build
	@node tests/admin-react-smoke.js

share-template-contract:
	@php -l src/Infrastructure/Definition/BuiltInNetworkProvider.php
	@php tests/share-template-contract.php

exclude-contract:
	@php -l src/Application/Content/ExcludedContentPolicy.php
	@php tests/exclude-contract.php

settings-local-checks:
	@pnpm run settings:check

ajax-contracts:
	@pnpm run test:ajax

multisite-contracts:
	@pnpm run test:multisite
