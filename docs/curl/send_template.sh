#!/usr/bin/env bash
set -euo pipefail

BASE_URL=${WABLAS_BASE_URL:-https://sby.wablas.com}
TOKEN=${WABLAS_TOKEN:-CHANGE_ME}
SECRET=${WABLAS_SECRET:-}
AUTH_PREFIX=${WABLAS_AUTH_PREFIX:-}

PHONE=${1:-628123456789}
TEMPLATE=${2:-order_update}
PARAMS_JSON=${3:-'{"1":"John","2":"#1234"}'}

curl -sS -X POST "$BASE_URL/api/send-template" \
  -H "Authorization: ${AUTH_PREFIX}${TOKEN}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "phone=$PHONE" \
  --data-urlencode "template=$TEMPLATE" \
  --data-urlencode "data=$PARAMS_JSON" \
  --data-urlencode "secret=$SECRET"

