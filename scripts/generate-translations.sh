#!/usr/bin/env bash

set -euo pipefail

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_root"

command -v wp >/dev/null
command -v msgmerge >/dev/null
command -v msgfmt >/dev/null

wp i18n make-pot . languages/html-social-share-buttons.pot \
	--domain=html-social-share-buttons \
	--exclude=node_modules,vendor,tests,docs,specs,scripts,security,.github,build \
	--package-name='HTML Social Share Buttons' \
	--headers='{"Report-Msgid-Bugs-To":"https://wordpress.org/support/plugin/html-social-share-buttons"}'

translation_epoch="${SOURCE_DATE_EPOCH:-946684800}"
translation_date="$(node -e 'const value = Number(process.argv[1]); if (!Number.isInteger(value) || value < 0) process.exit(1); process.stdout.write(new Date(value * 1000).toISOString().replace(".000Z", "+00:00"));' "$translation_epoch")"
HSSB_POT_DATE="$translation_date" perl -0pi -e 's/POT-Creation-Date:.*\\n/POT-Creation-Date: $ENV{HSSB_POT_DATE}\\n/g' \
	languages/html-social-share-buttons.pot
msgmerge --update --backup=none \
	languages/html-social-share-buttons-fr_FR.po \
	languages/html-social-share-buttons.pot
HSSB_POT_DATE="$translation_date" perl -0pi -e 's/POT-Creation-Date:.*\\n/POT-Creation-Date: $ENV{HSSB_POT_DATE}\\n/g' \
	languages/html-social-share-buttons-fr_FR.po
msgfmt --check \
	--output-file=languages/html-social-share-buttons-fr_FR.mo \
	languages/html-social-share-buttons-fr_FR.po
wp i18n make-json \
	languages/html-social-share-buttons-fr_FR.po \
	languages \
	--no-purge \
	--pretty-print \
	'--use-map={"src/js/blocks/shared/networks.js":["build/social-share.js","build/social-links.js"],"src/js/blocks/social-share/register.js":"build/social-share.js","src/js/blocks/social-links/register.js":"build/social-links.js"}'

php tests/javascript-localization-contract.php
