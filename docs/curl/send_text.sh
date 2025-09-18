#!/usr/bin/env bash
set -euo pipefail

# Env vars (override as needed)
BASE_URL=${WABLAS_BASE_URL:-https://sby.wablas.com}
TOKEN=${WABLAS_TOKEN:-CHANGE_ME}
SECRET=${WABLAS_SECRET:-}
AUTH_PREFIX=${WABLAS_AUTH_PREFIX:-} # set to 'Bearer ' if your device requires it

PHONE=${1:-628123456789}
MESSAGE=${2:-Halo dari curl}

curl -sS -X POST "$BASE_URL/api/send-message" \
  -H "Authorization: ${AUTH_PREFIX}${TOKEN}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data "phone=$PHONE&message=$(printf %s "$MESSAGE" | sed -e 's/[&]/\\&/g')&secret=$SECRET"

