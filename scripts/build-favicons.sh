#!/usr/bin/env bash

set -e

if command -v magick >/dev/null 2>&1; then
    echo "Generating favicons using ImageMagick..."
    magick public/favicon.svg -define icon:auto-resize=64,48,32,16 public/favicon.ico
    magick public/favicon.svg -resize 180x180 public/apple-touch-icon.png
else
    echo "ImageMagick not found, copying pre-generated favicons..."
    echo "Using existing files in public/"
fi
