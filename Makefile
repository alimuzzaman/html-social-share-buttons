.PHONY: frontend-capture frontend-compare frontend-drift-surface admin-react-smoke settings-sanitize-contract settings-local-checks help check-wp-root

WP_ROOT ?= $(shell [ -n "$(WP_ROOT)" ] && echo "$(WP_ROOT)" || echo "")

check-wp-root:
	@if [ -z "$(WP_ROOT)" ]; then \
		echo "WP_ROOT is required. Example: WP_ROOT=/var/www/html make frontend-capture"; \
		exit 1; \
	fi

help:
	@echo "Targets:"
	@echo "  frontend-capture   Capture frontend output baseline fixtures"
	@echo "  frontend-compare   Compare current output against baseline"
	@echo "  frontend-drift-surface   Verify frontend renderer files are unchanged"
	@echo "  admin-react-smoke  Verify React settings render and legacy field names"
	@echo "  settings-sanitize-contract  Verify saved settings keep legacy shape"
	@echo "  settings-local-checks  Run local settings checks that do not need WordPress DB"

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

settings-local-checks: admin-react-smoke settings-sanitize-contract frontend-drift-surface
