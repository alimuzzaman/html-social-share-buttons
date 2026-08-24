#!/usr/bin/env bash
set -euo pipefail

source_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
inter_archive="${1:-}"
expected_archive_sha="9883fdd4a49d4fb66bd8177ba6625ef9a64aa45899767dde3d36aa425756b11e"
tagline="HTML + CSS sharing. No frontend JS by default."

if [[ -z "$inter_archive" ]]; then
  echo "Usage: $0 /path/to/Inter-4.1.zip" >&2
  exit 64
fi

for command_name in hb-view shasum unzip; do
  if ! command -v "$command_name" >/dev/null 2>&1; then
    echo "Missing required command: $command_name" >&2
    exit 69
  fi
done

actual_archive_sha="$(shasum -a 256 "$inter_archive" | awk '{print $1}')"
if [[ "$actual_archive_sha" != "$expected_archive_sha" ]]; then
  echo "Inter archive checksum mismatch" >&2
  exit 65
fi

outline_temp_dir="$(mktemp -d "${TMPDIR:-/tmp}/hssb-tagline-outline.XXXXXX")"
cleanup() {
  rm -rf "$outline_temp_dir"
}
trap cleanup EXIT

font_file="$outline_temp_dir/Inter-Medium.ttf"
unzip -p "$inter_archive" extras/ttf/Inter-Medium.ttf > "$font_file"

hb-view "$font_file" \
  --text="$tagline" \
  --font-size=40 \
  --margin=0 \
  --foreground=CBD5E1 \
  --background=00000000 \
  --output-format=svg \
  --output-file="$source_dir/type/tagline.outlined.svg"

echo "Generated type/tagline.outlined.svg with $(hb-view --version | head -1)"
