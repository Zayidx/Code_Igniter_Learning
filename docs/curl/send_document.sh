#!/usr/bin/env bash
set -euo pipefail

BASE_URL=${WABLAS_BASE_URL:-https://sby.wablas.com}
TOKEN=${WABLAS_TOKEN:-CHANGE_ME}
SECRET=${WABLAS_SECRET:-}
AUTH_PREFIX=${WABLAS_AUTH_PREFIX:-}

PHONE=${1:-628123456789}
DOC_URL=${2:-https://example.com/file.pdf}
FILENAME=${3:-file.pdf}
CAPTION=${4:-Dokumen dari curl}

curl -sS -X POST "$BASE_URL/api/send-document" \
  -H "Authorization: ${AUTH_PREFIX}${TOKEN}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data "phone=$PHONE&document=$(printf %s "$DOC_URL" | sed -e 's/[&]/\\&/g')&filename=$(printf %s "$FILENAME" | sed -e 's/[&]/\\&/g')&caption=$(printf %s "$CAPTION" | sed -e 's/[&]/\\&/g')&secret=$SECRET"

