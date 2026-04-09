#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HOST="${HOST:-127.0.0.1}"
PORT="${1:-${PORT:-8000}}"

if [[ "${PORT}" == "--help" || "${PORT}" == "-h" ]]; then
  cat <<'EOF'
Usage:
  ./run.sh [port]

Examples:
  ./run.sh
  ./run.sh 8080

Environment overrides:
  HOST=0.0.0.0 ./run.sh
  PORT=9000 ./run.sh
EOF
  exit 0
fi

PKG_MANAGER=""
if command -v bun &>/dev/null; then
  PKG_MANAGER="bun"
elif command -v npm &>/dev/null; then
  PKG_MANAGER="npm"
  echo "Note: bun is not installed. npm will be used instead."
  echo "      Consider installing bun for faster builds: https://bun.sh"
fi

if [[ -n "${PKG_MANAGER}" ]]; then
  echo "Building frontend assets with ${PKG_MANAGER}..."
  cd "${PROJECT_ROOT}"
  if [[ "${PKG_MANAGER}" == "bun" ]]; then
    bun run build
  else
    npm run build
  fi
else
  echo "Neither bun nor npm is installed."
  echo "Frontend assets will not be rebuilt. Using previously compiled build."
fi

if ! command -v php &>/dev/null; then
  echo "Error: php is not installed or not in PATH." >&2
  echo "       Install PHP to run the development server: https://www.php.net" >&2
  exit 1
fi

echo "Starting HappyCMS at http://${HOST}:${PORT}"
echo "Press Ctrl+C to stop."

exec php -S "${HOST}:${PORT}" -t "${PROJECT_ROOT}" "${PROJECT_ROOT}/router.php"