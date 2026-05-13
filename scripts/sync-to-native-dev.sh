#!/usr/bin/env bash
# Kopeerib selle repo failid ~/dev/ta-24-frame-test alla (node_modules, vendor, .env ei puutu).
# Kasuta kui Cursor avatud /mnt/c/... peal aga `npm run dev` käib ~/dev-is.
set -euo pipefail
SRC="$(cd "$(dirname "$0")/.." && pwd)"
DST="${DEV_REPO:-$HOME/dev/ta-24-frame-test}"
if [[ ! -d "$DST" ]]; then
    echo "Sihtkaust puudub: $DST — sea DEV_REPO või loo kaust." >&2
    exit 1
fi
rsync -a --delete-delay \
    --exclude node_modules --exclude vendor --exclude .git \
    --exclude .env --exclude 'database/*.sqlite' \
    --exclude 'storage/logs' --exclude 'storage/framework/cache' \
    --exclude 'storage/framework/sessions' --exclude 'storage/framework/views' \
    "$SRC/" "$DST/"
echo "OK: $SRC -> $DST"
echo "Taaskäivita ~/dev-is: npm run dev"
