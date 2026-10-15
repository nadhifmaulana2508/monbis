# Prompt kerja migrasi MONBIS

Folder ini berisi prompt kerja yang memakai:

- [PRD Migrasi PHP ke React](../docs/prd-migrasi-php-react.md)
- [PRD UI Laporan Kredit](../docs/prd-modernisasi-4-laporan-kredit.md)
- feature report React pada `frontend/src/features/`
- aplikasi KPI Tukin mandiri pada `kpi-tukin/`

Gunakan prompt berdasarkan satu page atau satu scope. Jangan menjalankan
migrasi beberapa page sekaligus dalam satu prompt karena parity data dan
rollback menjadi sulit diperiksa.

Format tag:

```text
[MONBIS-UI] [MIGRASI:PHP-REACT] [PAGE:<slug>] [SCOPE:<scope>]
```

Prompt utama ada di [migrasi-php-ke-react.md](migrasi-php-ke-react.md).

Contoh prompt lengkap KPI pertama ada di
[kpi-setting-jabatan.md](kpi-setting-jabatan.md). Prompt tersebut memakai
dua tahap: `AUDIT_ONLY` lebih dulu, lalu `IMPLEMENT` setelah audit disetujui.
Implementasi KPI selalu mengikuti `kpi-tukin/` dan tidak mengimpor source
code dari `frontend/src/`.
