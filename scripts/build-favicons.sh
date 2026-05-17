#!/usr/bin/env bash

set -e

if command -v magick >/dev/null 2>&1; then
    echo "Generating favicons using ImageMagick..."
    magick -size 512x512 xc:none \
        -fill '#1e2530' -draw 'roundrectangle 0,0 512,512 112,112' \
        -fill none -stroke '#f7f2ea' -strokewidth 56 \
        -draw 'circle 184,256 184,144' \
        -draw "path 'M392 144 C320 144 296 192 352 232 C416 272 392 368 296 368'" \
        public/icon-512.png
    magick public/icon-512.png -define icon:auto-resize=64,48,32,16 public/favicon.ico
    magick public/icon-512.png -resize 180x180 public/apple-touch-icon.png
else
    echo "ImageMagick not found, copying pre-generated favicons..."
    echo "Using existing files in public/"
fi
