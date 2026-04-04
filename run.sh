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

echo "Building frontend assets..."
cd "${PROJECT_ROOT}"
bun run build

echo "Starting HappyCMS at http://${HOST}:${PORT}"
echo "Press Ctrl+C to stop."

exec php -S "${HOST}:${PORT}" -t "${PROJECT_ROOT}" "${PROJECT_ROOT}/router.php"
