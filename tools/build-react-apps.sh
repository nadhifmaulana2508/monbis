#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
INSTALL_DEPS=true

case "${1:-}" in
  "") ;;
  --skip-install) INSTALL_DEPS=false ;;
  *) printf 'Usage: bash tools/build-react-apps.sh [--skip-install]\n' >&2; exit 2 ;;
esac

if ! command -v node >/dev/null 2>&1 || ! command -v npm >/dev/null 2>&1; then
  printf 'Node.js dan npm harus tersedia di PATH.\n' >&2
  exit 1
fi

node -e 'const [major, minor] = process.versions.node.split(".").map(Number); const supported = (major === 20 && minor >= 19) || (major >= 22 && (major > 22 || minor >= 12)); if (!supported) { console.error(`Node.js ${process.versions.node} tidak didukung Vite 8. Gunakan Node.js >=20.19 atau >=22.12.`); process.exit(1); }'

APPS=("kpi-tukin" "monbis-v2" "rbb-bisnis")

for app in "${APPS[@]}"; do
  app_dir="$ROOT_DIR/$app"
  if [[ ! -f "$app_dir/package.json" || ! -f "$app_dir/package-lock.json" ]]; then
    printf 'package.json atau package-lock.json tidak ditemukan: %s\n' "$app" >&2
    exit 1
  fi

  printf '\n==> Build %s\n' "$app"
  if [[ "$INSTALL_DEPS" == true ]]; then
    (cd "$app_dir" && npm ci --no-audit --no-fund)
  fi
  (cd "$app_dir" && npm run build)
done

printf '\nSemua aplikasi React berhasil dibuild.\n'
