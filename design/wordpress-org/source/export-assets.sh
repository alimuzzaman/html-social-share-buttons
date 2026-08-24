#!/usr/bin/env bash
set -euo pipefail

source_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
asset_dir="$(cd "$source_dir/.." && pwd)"

node "$source_dir/build-artwork.mjs"

rsvg-convert --width 1544 --height 500 \
  "$source_dir/banner-master.svg" \
  --output "$asset_dir/banner-1544x500.png"

rsvg-convert --width 772 --height 250 \
  "$source_dir/banner-master.svg" \
  --output "$asset_dir/banner-772x250.png"

rsvg-convert --width 256 --height 256 \
  "$asset_dir/icon.svg" \
  --output "$asset_dir/icon-256x256.png"

rsvg-convert --width 128 --height 128 \
  "$asset_dir/icon.svg" \
  --output "$asset_dir/icon-128x128.png"

node "$source_dir/tag-srgb.mjs" \
  "$asset_dir/banner-1544x500.png" \
  "$asset_dir/banner-772x250.png" \
  "$asset_dir/icon-256x256.png" \
  "$asset_dir/icon-128x128.png"

echo "Exported WordPress.org assets to $asset_dir"
