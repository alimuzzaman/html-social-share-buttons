#!/usr/bin/env bash
set -euo pipefail

frontend_files=(
	"actions.php"
	"filters.php"
	"iconsets.php"
	"interfaces.php"
)

changed=()
for file in "${frontend_files[@]}"; do
	diff_output=$(git diff -- "$file")
	meaningful_diff=$(printf '%s\n' "$diff_output" | sed -E '/^(diff --git|index |--- |\+\+\+ |@@ )/d' | grep -E '^[+-]' | grep -Ev '^\+[[:space:]]*// phpcs:ignore ' || true)
	if [ -n "$meaningful_diff" ]; then
		changed+=("$file")
	fi
done

if [ "${#changed[@]}" -gt 0 ]; then
	printf 'Frontend drift surface changed:\n'
	printf ' - %s\n' "${changed[@]}"
	exit 1
fi

printf 'Frontend drift surface unchanged: %s files checked.\n' "${#frontend_files[@]}"
