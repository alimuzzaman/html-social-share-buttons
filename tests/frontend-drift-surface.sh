#!/usr/bin/env bash
set -euo pipefail

frontend_files=(
	"actions.php"
	"filters.php"
	"function.php"
	"iconsets.php"
	"interfaces.php"
	"shortcode.php"
	"widget.php"
)

changed=()
for file in "${frontend_files[@]}"; do
	if ! git diff --quiet -- "$file"; then
		changed+=("$file")
	fi
done

if [ "${#changed[@]}" -gt 0 ]; then
	printf 'Frontend drift surface changed:\n'
	printf ' - %s\n' "${changed[@]}"
	exit 1
fi

printf 'Frontend drift surface unchanged: %s files checked.\n' "${#frontend_files[@]}"
