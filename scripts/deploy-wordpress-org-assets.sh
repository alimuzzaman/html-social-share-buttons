#!/usr/bin/env bash

set -euo pipefail

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
publish=false
watch=false

usage() {
	cat <<'EOF'
Usage: scripts/deploy-wordpress-org-assets.sh [--publish] [--watch]

Without --publish, validate the WordPress.org asset package only.
--publish dispatches the manual GitHub Actions asset workflow from origin/master.
--watch waits for that workflow run and returns its result.
EOF
}

while (( $# )); do
	case "$1" in
		--publish) publish=true ;;
		--watch) watch=true ;;
		-h|--help) usage; exit 0 ;;
		*) echo "Unknown argument: $1" >&2; usage >&2; exit 2 ;;
	esac
	shift
done

if [[ "$watch" == true && "$publish" != true ]]; then
	echo '--watch requires --publish.' >&2
	exit 2
fi

cd "$repo_root"

expected=(
	banner-1544x500.png
	banner-772x250.png
	blueprints/blueprint.json
	icon-128x128.png
	icon-256x256.png
	icon.svg
	screenshot-1.png
	screenshot-2.png
	screenshot-3.png
	screenshot-4.png
)

actual="$(find .wordpress-org -type f -print | sed 's#^\.wordpress-org/##' | LC_ALL=C sort)"
wanted="$(printf '%s\n' "${expected[@]}" | LC_ALL=C sort)"
if [[ "$actual" != "$wanted" ]]; then
	echo 'The .wordpress-org asset manifest does not match the expected release files.' >&2
	diff -u <(printf '%s\n' "$wanted") <(printf '%s\n' "$actual") || true
	exit 1
fi

for asset in banner-1544x500.png banner-772x250.png icon-128x128.png icon-256x256.png icon.svg; do
	cmp "design/wordpress-org/$asset" ".wordpress-org/$asset"
done
for asset in screenshot-1.png screenshot-2.png screenshot-3.png screenshot-4.png; do
	cmp "design/wordpress-org/screenshots/$asset" ".wordpress-org/$asset"
done

node design/wordpress-org/source/validate-assets.mjs
jq empty .wordpress-org/blueprints/blueprint.json
echo 'WordPress.org asset package validation passed.'

if [[ "$publish" != true ]]; then
	exit 0
fi

command -v gh >/dev/null || { echo 'GitHub CLI (gh) is required to publish assets.' >&2; exit 1; }
[[ -z "$(git status --porcelain)" ]] || { echo 'Refusing to publish from a dirty worktree.' >&2; exit 1; }
[[ "$(git branch --show-current)" == master ]] || { echo 'Refusing to publish outside the master branch.' >&2; exit 1; }

git fetch origin master --quiet
[[ "$(git rev-parse HEAD)" == "$(git rev-parse origin/master)" ]] || {
	echo 'Refusing to publish until local master exactly matches origin/master.' >&2
	exit 1
}

run_url="$(gh workflow run wordpress-org-assets.yml --ref master)"
echo "Dispatched: $run_url"

if [[ "$watch" == true ]]; then
	run_id="${run_url##*/}"
	gh run watch "$run_id" --exit-status
fi
