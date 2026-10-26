# MONBIS V2

Frontend React baru untuk migrasi halaman MONBIS secara bertahap.

Panduan wajib saat memindahkan menu lama ke frontend ini: [issue.md](./issue.md).
Dokumen itu khusus React `monbis-v2/`; jangan tertukar dengan `../issue.md` (report
PHP `mb_*`) atau `../issue_monbis_v2.md` (frontend PHP `v-2/`).

Fondasi ini memakai komponen UI yang sudah tersedia di project `frontend`, Tailwind CSS, dan konfigurasi shadcn. Halaman PHP tidak dipindahkan sekaligus supaya setiap migrasi bisa divalidasi dari data API, route, permission RBAC, loading, error, empty state, dan responsive layout.

## Menjalankan lokal

```bash
npm install
npm run dev
```

Dev server lokal tetap memakai route root, misalnya `http://localhost:5173/pipeline-kredit`.
Build deployment memakai base `/v2-react/`, sehingga route production menjadi
`https://monbis.bkkjateng.co.id/v2-react/pipeline-kredit` dan route dev menjadi
`https://dev.monbis.bkkjateng.co.id/v2-react/pipeline-kredit`. Apache perlu menyajikan
file dari `dist/` serta mengarahkan deep link `/v2-react/*` ke `dist/index.html`.

## Verifikasi

```bash
npm run lint
npm run build
```

Route React yang sudah tersedia saat fondasi dibuat:

- `/realisasi-kredit-growth`
- `/jatuh-tempo`

This template provides a minimal setup to get React working in Vite with HMR and some Oxlint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Oxc](https://oxc.rs)
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/)

## React Compiler

The React Compiler is not enabled on this template because of its impact on dev & build performances. To add it, see [this documentation](https://react.dev/learn/react-compiler/installation).

## Expanding the Oxlint configuration

If you are developing a production application, we recommend enabling type-aware lint rules by installing `oxlint-tsgolint` and editing `.oxlintrc.json`:

```json
{
  "$schema": "./node_modules/oxlint/configuration_schema.json",
  "plugins": ["react", "typescript", "oxc"],
  "options": {
    "typeAware": true
  },
  "rules": {
    "react/rules-of-hooks": "error",
    "react/only-export-components": ["warn", { "allowConstantExport": true }]
  }
}
```

See the [Oxlint rules documentation](https://oxc.rs/docs/guide/usage/linter/rules) for the full list of rules and categories.
