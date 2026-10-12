# Rencana Refactor UI MONBIS → Standar Modern (shadcn-style)

> **Status**: Draft
> **Tanggal**: 12 Oktober 2026
> **Goal**: Merombak tampilan semua halaman MONBIS menjadi modern & konsisten,
> **tanpa menyentuh lapisan logic** (Controller, API, Database tetap 100% sama).

---

## Daftar Isi

1. [Ringkasan Strategi](#1-ringkasan-strategi)
2. [Prinsip Kerja](#2-prinsip-kerja)
3. [Tech Stack & Setup Foundation](#3-tech-stack--setup-foundation)
4. [Desain System (Token & Class)](#4-desain-system-token--class)
5. [PHP UI Helpers](#5-php-ui-helpers)
6. [Playbook Perubahan per Komponen](#6-playbook-perubahan-per-komponen)
7. [Roadmap Migrasi per Halaman](#7-roadmap-migrasi-per-halaman)
8. [Timeline & Milestone](#8-timeline--milestone)
9. [Quality Checklist](#9-quality-checklist)
10. [Risiko & Mitigasi](#10-risiko--mitigasi)
11. [File yang Akan Dibuat/Diubah](#11-file-yang-akan-dibuatdiubah)

---

## 1. Ringkasan Strategi

### Masalah
UI MONBIS saat ini tidak konsisten: class Tailwind ditulis manual per halaman,
banyak file duplikat, styling ad-hoc, dan tidak ada standar visual.

### Solusi: "shadcn aesthetic, PHP-native implementation"

| Keputusan | Alasan |
|-----------|--------|
| **Ambil design system shadcn** (CSS variables, radius, color token, anatomy komponen) | Visual modern, konsisten, industry standard |
| **Implementasi via Tailwind class + PHP helper functions** | Zero build step, zero Node.js, team tetap PHP |
| **Interaktivitas via Alpine.js** (modal, dropdown, tabs, datepicker) | Ringan (14kb), syntax deklaratif, tidak butuh React |
| **Date picker via Flatpickr** | Sudah teruji, lokal Indonesia, tanpa dependency berat |
| **TIDAK pakai shadcn/ui React component** | Butuh build step, Node, dan arsitektur React — overkill |

### Apa yang TIDAK berubah
- ✅ Semua file di `api/` (routes, controllers, helpers, middlewares) — **tidak disentuh**
- ✅ Semua query SQL dan logic bisnis — **tidak disentuh**
- ✅ Format response API `{ status, message, data }` — **tidak berubah**
- ✅ Router `index.php` & `api/index.php` — **tidak berubah**
- ✅ Autentikasi SSO & JWT — **tidak berubah**
- ✅ `v-2/index.php` router — **tidak berubah**

### Apa yang berubah
- 🔨 Class Tailwind di semua file `pages/*.php`, `views/*.php`, `v-2/pages/*.php`
- 🔨 Tambah 2 dependency CDN: Alpine.js + Flatpickr (sekali di layout)
- 🔨 Tambah 1 file helper: `views/components/ui.php`
- 🔨 Tampilan visual jadi konsisten (shadcn look & feel)

---

## 2. Prinsip Kerja

### Aturan Emas

1. **Logic = PHP asli.** Jangan pindahkan perhitungan/fetch ke JS.
   Fetch API yang sudah ada (`fetch('/api/?request=...')`) tetap dipertahankan apa adanya.
2. **UI = class Tailwind baru + helper `ui_*()`.** Ganti class lama → class shadcn-style.
3. **Interaksi client-side pakai Alpine.** Contoh: buka/tutup modal, toggle sidebar,
   sort table client-side, copy-to-clipboard.
4. **Satu halaman selesai = test manual** (render benar, fetch API jalan, responsif).
5. **Commit per halaman.** Kalau ada bug, mudah rollback.
6. **Jangan ubah struktur query/response.** Hanya bungkus tampilan.

### Definisi "Selesai" per Halaman

- [ ] Card pakai token `bg-card border text-card-foreground shadow-sm`
- [ ] Button pakai helper `ui_button()` (variant: default / outline / ghost / destructive)
- [ ] Badge/status pakai `ui_badge()` (success / warning / destructive / info)
- [ ] Table pakai `ui_table()` + `ui_th()` / `ui_td()` (ada hover row)
- [ ] Input & select pakai `ui_input()` / `ui_select()`
- [ ] Modal pakai `ui_modal()` + Alpine `x-show` / `x-transition`
- [ ] Filter tanggal pakai Flatpickr (format `Y-m-d`, lokal `id`)
- [ ] Halaman responsif (breakpoint `md:` / `lg:` minimal)
- [ ] Fetch API & logic lama tetap berfungsi 100%

---

## 3. Tech Stack & Setup Foundation

### 3.1 Tambah dependency CDN (sekali saja)

Tambahkan di `views/header.php` (**setelah** Tailwind CDN/tag yang sudah ada):

```html
<!-- Alpine.js: interaktivitas client-side (modal, dropdown, tabs, toggle) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Flatpickr: date picker konsisten -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
```

Untuk `v-2/`, tambahkan hal yang sama di `v-2/components/bootstrap.php`
(atau layout V2 yang relevan).

### 3.2 Buat file helper UI

```
views/components/ui.php   ← file baru (helper functions)
```

Include sekali di `views/header.php`:

```php
require_once __DIR__ . '/components/ui.php';
```

### 3.3 Catatan Tailwind

Project sudah memakai Tailwind. Pastikan `content` / `safelist` mencakup:

```
pages/**/*.php
views/**/*.php
components/**/*.php
v-2/**/*.php
```

Kalau memakai Tailwind CDN, tidak perlu config tambahan.
Kalau memakai Tailwind CLI/build, tambahkan path di atas ke `content`.

---

## 4. Desain System (Token & Class)

### 4.1 CSS Variables (taruh di `views/header.php` dalam `<style>`)

```css
:root {
  --radius: 0.5rem;
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;
  --card: 0 0% 100%;
  --card-foreground: 222.2 84% 4.9%;
  --popover: 0 0% 100%;
  --popover-foreground: 222.2 84% 4.9%;
  --primary: 221.2 83.2% 53.3%;
  --primary-foreground: 210 40% 98%;
  --secondary: 210 40% 96.1%;
  --secondary-foreground: 222.2 47.4% 11.2%;
  --muted: 210 40% 96.1%;
  --muted-foreground: 215.4 16.3% 46.9%;
  --accent: 210 40% 96.1%;
  --accent-foreground: 222.2 47.4% 11.2%;
  --destructive: 0 84.2% 60.2%;
  --destructive-foreground: 210 40% 98%;
  --success: 142.1 70.6% 35.3%;
  --warning: 38 92% 50%;
  --border: 214.3 31.8% 91.4%;
  --input: 214.3 31.8% 91.4%;
  --ring: 221.2 83.2% 53.3%;
}

.dark {
  --background: 222.2 84% 4.9%;
  --foreground: 210 40% 98%;
  --card: 222.2 84% 4.9%;
  --card-foreground: 210 40% 98%;
  --popover: 222.2 84% 4.9%;
  --popover-foreground: 210 40% 98%;
  --primary: 217.2 91.2% 59.8%;
  --primary-foreground: 222.2 47.4% 11.2%;
  --secondary: 217.2 32.6% 17.5%;
  --secondary-foreground: 210 40% 98%;
  --muted: 217.2 32.6% 17.5%;
  --muted-foreground: 215 20.2% 65.1%;
  --accent: 217.2 32.6% 17.5%;
  --accent-foreground: 210 40% 98%;
  --border: 217.2 32.6% 17.5%;
  --input: 217.2 32.6% 17.5%;
  --ring: 224.3 76.3% 48.1%;
}
```

### 4.2 Mapping Class Lama → Baru

| Komponen | Class Lama (contoh) | Class / Helper Baru |
|----------|--------------------|---------------------|
| Card | `bg-white rounded-lg shadow p-6` | `rounded-lg border bg-card text-card-foreground shadow-sm p-6` |
| Button primary | `bg-blue-600 text-white px-4 py-2 rounded` | `ui_button('Label')` |
| Button outline | `border border-gray-300 bg-white hover:bg-gray-50` | `ui_button('Label', ['variant'=>'outline'])` |
| Button destructive | `bg-red-600 text-white hover:bg-red-700` | `ui_button('Hapus', ['variant'=>'destructive'])` |
| Badge sukses | `bg-green-100 text-green-800 px-2 py-1 rounded` | `ui_badge('Lancar', 'success')` |
| Badge buruk | `bg-red-100 text-red-800` | `ui_badge('Macet', 'destructive')` |
| Input | `border rounded px-3 py-2 w-full` | `ui_input(['class'=>'w-full'])` |
| Select | `border rounded px-3 py-2` | `ui_select($options, $selected, [...])` |
| Table | `<table class="w-full border">` | `ui_table(...)` + `ui_th` / `ui_td` |
| Modal | manual overlay + `display:none` | `ui_modal(...)` + Alpine |
| KPI box | `bg-white p-4 rounded-lg shadow` | Card + `text-3xl font-bold` + `text-muted-foreground` label |
| Muted box | `bg-gray-50 p-4 rounded` | `bg-muted p-4 rounded-md` |
| Heading section | `text-xl font-bold text-gray-800` | `text-xl font-semibold tracking-tight` |

### 4.3 Konvensi Warna Status (domain kredit)

| Status | Badge Variant | Pemakaian |
|--------|---------------|-----------|
| Lancar / L / aman / on-track | `success` | Kolektibilitas L, RR naik, OTP bagus |
| Perhatian / DP / warning | `warning` | Kolektibilitas DP, mendekati target |
| Kurang lancar / D / M / NPL | `destructive` | KL, D, M; NPL naik; gagal bayar |
| Netral / info / non-kategoris | `info` | Kolektibilitas KL (border), data proses |

---

## 5. PHP UI Helpers

File: `views/components/ui.php`

```php
<?php
declare(strict_types=1);

/**
 * ============================================================
 * MONBIS UI HELPERS (shadcn-style, PHP-native, no build step)
 * ============================================================
 * Dipakai di halaman pages/*.php via:
 *   require_once __DIR__ . '/../views/components/ui.php';
 * (Atau sudah auto-include dari views/header.php)
 *
 * Helper ini HANYA menghasilkan HTML string.
 * Tidak ada logic bisnis di sini.
 * ============================================================
 */

// ---------- ATOM ----------

function ui_button(string $content, array $attrs = []): string
{
    $variant = $attrs['variant'] ?? 'default';
    $size    = $attrs['size'] ?? 'default';
    unset($attrs['variant'], $attrs['size']);

    $variants = [
        'default'     => 'bg-primary text-primary-foreground hover:bg-primary/90',
        'outline'     => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
        'ghost'       => 'hover:bg-accent hover:text-accent-foreground',
        'link'        => 'text-primary underline-offset-4 hover:underline',
        'secondary'   => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
    ];
    $sizes = [
        'default' => 'h-10 px-4 py-2',
        'sm'      => 'h-9 px-3',
        'lg'      => 'h-11 px-8',
        'icon'    => 'h-10 w-10',
    ];

    $class = 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium '
        . 'ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 '
        . 'focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 '
        . ($variants[$variant] ?? $variants['default']) . ' '
        . ($sizes[$size] ?? $sizes['default']);

    return ui_render_tag('button', $content, $attrs, $class);
}

function ui_input(array $attrs = []): string
{
    $class = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm '
        . 'ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none '
        . 'focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 '
        . 'disabled:cursor-not-allowed disabled:opacity-50';

    return ui_render_tag('input', '', $attrs, $class, true);
}

function ui_badge(string $content, string $variant = 'default', array $attrs = []): string
{
    $variants = [
        'default'     => 'border-transparent bg-primary text-primary-foreground',
        'secondary'   => 'border-transparent bg-secondary text-secondary-foreground',
        'destructive' => 'border-transparent bg-destructive text-destructive-foreground',
        'outline'     => 'text-foreground',
        'success'     => 'border-transparent bg-green-600 text-white',
        'warning'     => 'border-transparent bg-amber-500 text-white',
        'info'        => 'border-transparent bg-blue-600 text-white',
        'muted'       => 'border-transparent bg-muted text-muted-foreground',
    ];

    $class = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold '
        . 'transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 '
        . ($variants[$variant] ?? $variants['default']);

    return ui_render_tag('span', $content, $attrs, $class);
}

// ---------- CARD ----------

function ui_card(string $content, array $attrs = []): string
{
    $class = 'rounded-lg border bg-card text-card-foreground shadow-sm';
    return ui_render_tag('div', $content, $attrs, $class);
}

function ui_card_header(string $content, array $attrs = []): string
{
    $class = 'flex flex-col space-y-1.5 p-6';
    return ui_render_tag('div', $content, $attrs, $class);
}

function ui_card_title(string $content, array $attrs = []): string
{
    $class = 'text-2xl font-semibold leading-none tracking-tight';
    return ui_render_tag('h3', $content, $attrs, $class);
}

function ui_card_description(string $content, array $attrs = []): string
{
    $class = 'text-sm text-muted-foreground';
    return ui_render_tag('p', $content, $attrs, $class);
}

function ui_card_content(string $content, array $attrs = []): string
{
    $class = 'p-6 pt-0';
    return ui_render_tag('div', $content, $attrs, $class);
}

function ui_card_footer(string $content, array $attrs = []): string
{
    $class = 'flex items-center p-6 pt-0';
    return ui_render_tag('div', $content, $attrs, $class);
}

// ---------- KPI BOX (khas MONBIS) ----------

/**
 * KPI box untuk dashboard: label kecil, angka besar, delta +/-.
 * Contoh:
 *   ui_kpi_box('NPL', '3.2%', '+0.4%', 'destructive');
 */
function ui_kpi_box(
    string $label,
    string $value,
    ?string $delta = null,
    string $deltaTone = 'neutral',
    array $attrs = []
): string {
    $toneClass = match ($deltaTone) {
        'destructive' => 'text-destructive',
        'success'     => 'text-green-600',
        default       => 'text-muted-foreground',
    };

    $deltaHtml = '';
    if ($delta !== null && $delta !== '') {
        $deltaHtml = '<span class="text-sm font-medium ' . $toneClass . '">' . e($delta) . '</span>';
    }

    $content =
        '<div class="text-sm font-medium text-muted-foreground">' . e($label) . '</div>'
        . '<div class="text-3xl font-bold tracking-tight">' . e($value) . '</div>'
        . ($deltaHtml !== '' ? '<div class="mt-1">' . $deltaHtml . '</div>' : '');

    return ui_card($content, ['class' => 'p-6'] + $attrs);
}

// ---------- TABLE ----------

function ui_table(string $content, array $attrs = []): string
{
    $class = 'w-full caption-bottom text-sm';
    $wrap  = '<div class="w-full overflow-auto rounded-lg border">'
        . '<table class="' . $class . '">' . $content . '</table></div>';

    // $attrs hanya untuk atribut div pembungkus bila perlu
    return $wrap;
}

function ui_thead(string $content): string
{
    return '<thead class="[&_tr]:border-b bg-muted/50">' . $content . '</thead>';
}

function ui_tbody(string $content): string
{
    return '<tbody class="[&_tr:last-child]:border-0">' . $content . '</tbody>';
}

function ui_tr(string $content, array $attrs = []): string
{
    $class = 'border-b transition-colors hover:bg-muted/50';
    return ui_render_tag('tr', $content, $attrs, $class);
}

function ui_th(string $content, array $attrs = []): string
{
    $class = 'h-12 px-4 text-left align-middle font-medium text-muted-foreground '
        . '[&:has([role=checkbox])]:pr-0';
    return ui_render_tag('th', $content, $attrs, $class);
}

function ui_td(string $content, array $attrs = []): string
{
    $class = 'p-4 align-middle [&:has([role=checkbox])]:pr-0';
    return ui_render_tag('td', $content, $attrs, $class);
}

// ---------- SELECT (wrapper native select) ----------

function ui_select(array $options, $selected = null, array $attrs = []): string
{
    $inner = '';
    foreach ($options as $value => $label) {
        $sel = ((string)$value === (string)$selected) ? ' selected' : '';
        $inner .= '<option value="' . e((string)$value) . '"' . $sel . '>' . e((string)$label) . '</option>';
    }

    $class = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm '
        . 'ring-offset-background focus-visible:outline-none focus-visible:ring-2 '
        . 'focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';

    $attrs['class'] = trim(($attrs['class'] ?? '') . ' ' . $class);
    return ui_render_tag('select', $inner, $attrs, '');
}

// ---------- MODAL (Alpine.js) ----------

/**
 * Modal siap pakai dengan Alpine.
 * Trigger: panggil dari tombol via Alpine, misal:
 *   <button @click="$dispatch('mb-open', 'modalId')">Buka</button>
 *   (atau set x-ref, lalu $refs.modalId.open = true)
 *
 * Contoh:
 *   echo ui_modal('filterModal', 'Filter Data', $bodyHtml, $footerHtml);
 *   // Lalu di parent container: x-data="{ filterModal: false }"
 */
function ui_modal(string $id, string $title, string $content, string $footer = ''): string
{
    $footerHtml = $footer !== ''
        ? '<div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">' . $footer . '</div>'
        : '';

    return <<<HTML
<div x-data="{ open: false }" x-cloak>
  <div x-show="open"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
       @click="open = false"
       class="fixed inset-0 z-50 bg-black/70"></div>
  <div x-show="open"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
       x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
       role="dialog" aria-modal="true" aria-label="{$title}"
       class="fixed left-1/2 top-1/2 z-50 grid w-full max-w-lg -translate-x-1/2 -translate-y-1/2 gap-4 border bg-background p-6 shadow-lg duration-200 sm:rounded-lg">
    <div class="flex flex-col space-y-1.5 text-left">
      <h2 class="text-lg font-semibold leading-none tracking-tight">{$title}</h2>
    </div>
    <div class="text-sm">{$content}</div>
    {$footerHtml}
  </div>
</div>
HTML;
}

// ---------- DATE RANGE (Flatpickr) ----------

/**
 * Komponen filter tanggal Closing + Harian (dipakai hampir semua halaman).
 * Data tetap lewat query string → fetch API lama tidak berubah.
 *
 * Contoh:
 *   echo ui_date_range(
 *     ['closing_date' => $closing_date, 'harian_date' => $harian_date],
 *     BASE_APP . '/dashboard'
 *   );
 */
function ui_date_range(array $dates, string $reloadUrl): string
{
    $closing = e($dates['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month')));
    $harian  = e($dates['harian_date'] ?? date('Y-m-d'));

    return <<<HTML
<div x-data="{
  closing: '{$closing}',
  harian: '{$harian}',
  init() {
    flatpickr(this.$refs.fpClosing, {
      locale: 'id', dateFormat: 'Y-m-d',
      onChange: (d) => { this.closing = d[0] ? flatpickr.formatDate(d[0], 'Y-m-d') : this.closing; }
    });
    flatpickr(this.$refs.fpHarian, {
      locale: 'id', dateFormat: 'Y-m-d',
      onChange: (d) => { this.harian = d[0] ? flatpickr.formatDate(d[0], 'Y-m-d') : this.harian; }
    });
  },
  apply() {
    const url = new URL('{$reloadUrl}', location.origin);
    url.searchParams.set('closing_date', this.closing);
    url.searchParams.set('harian_date', this.harian);
    location.href = url.toString();
  }
}" class="flex flex-wrap items-end gap-3">
  <div class="grid gap-1.5">
    <label class="text-sm font-medium leading-none">Closing (M-1)</label>
    <input x-ref="fpClosing" class="flex h-10 w-[150px] rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="YYYY-MM-DD">
  </div>
  <div class="grid gap-1.5">
    <label class="text-sm font-medium leading-none">Harian</label>
    <input x-ref="fpHarian" class="flex h-10 w-[150px] rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="YYYY-MM-DD">
  </div>
  <button type="button" @click="apply()" class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">
    Terapkan
  </button>
</div>
HTML;
}

// ---------- UTIL (kecil) ----------

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Renderer tag generik supaya atribut + class konsisten.
 * Kalau $attrs['class'] ada, digabung dengan $class default.
 */
function ui_render_tag(string $tag, string $content, array $attrs, string $defaultClass, bool $selfClosing = false): string
{
    $extraClass = (string)($attrs['class'] ?? '');
    unset($attrs['class']);

    $class = trim($defaultClass . ' ' . $extraClass);

    $attrStr = '';
    foreach ($attrs as $key => $value) {
        if ($value === null || $value === false) continue;
        if ($value === true) {
            $attrStr .= ' ' . $key;
            continue;
        }
        $attrStr .= ' ' . $key . '="' . e((string)$value) . '"';
    }

    if ($class !== '') {
        $attrStr .= ' class="' . $class . '"';
    }

    if ($selfClosing || in_array($tag, ['input', 'img', 'br', 'hr'], true)) {
        return '<' . $tag . $attrStr . '>';
    }

    return '<' . $tag . $attrStr . '>' . $content . '</' . $tag . '>';
}
```

---

## 6. Playbook Perubahan per Komponen

### 6.1 KPI Box (Dashboard)

**Sebelum:**
```php
<div class="bg-white p-6 rounded-lg shadow-md mb-4">
  <div class="text-sm text-gray-500">Total NPL</div>
  <div class="text-3xl font-bold text-red-600">3.2%</div>
  <div class="text-xs text-gray-400">vs bulan lalu</div>
</div>
```

**Sesudah:**
```php
echo ui_kpi_box('Total NPL', '3.2%', '+0.4%', 'destructive');
```

### 6.2 Button Aksi

```php
// Sebelum
<button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
<button class="border px-4 py-2 rounded">Reset</button>
<button class="bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</button>

// Sesudah
echo ui_button('Filter');
echo ui_button('Reset', ['variant' => 'outline']);
echo ui_button('Hapus', ['variant' => 'destructive', 'size' => 'sm']);
```

### 6.3 Table Data

```php
// Sesudah
echo ui_table(
    ui_thead(
        ui_tr(ui_th('Cabang') . ui_th('NPL %', ['class' => 'text-right']) . ui_th('Status'))
    ) .
    ui_tbody(
        ui_tr(
            ui_td('KC Semarang') .
            ui_td('3.2%', ['class' => 'text-right text-destructive font-medium']) .
            ui_td(ui_badge('Kritis', 'destructive'))
        )
    )
);
```

> Catatan: untuk tabel dengan rendering via JS/Ajax (seperti DataTables manual),
> hanya bungkus `<table>` & header baris dengan class `ui_th` / `ui_td` —
> logic sorting/filtering yang sudah ada tetap dipakai.

### 6.4 Modal

```php
// Tambahkan trigger (di dalam wadah x-data yang sama, atau elemen lain)
echo '<div x-data="{ filterOpen: false }">';
echo '  <button type="button" @click="filterOpen = true">' . ui_button('Filter', ['variant' => 'outline']) . '</button>';
echo '  <div x-data="{ filterOpen: false }" x-show="false"></div>'; // (lihat catatan)
echo '</div>';
```

> **Catatan penting:** Agar trigger & modal Alpine dalam scope yang sama,
> bungkus keduanya dalam satu `x-data`. Pola yang lebih rapi:
>
> ```php
> echo '<div x-data="{ filterOpen: false }">';
> echo '  <button type="button" @click="filterOpen = true">Buka Filter</button>';
> // modal custom yang mengikat open=filterOpen
> echo '</div>';
> ```
>
> Alternatif: gunakan `ui_modal()` sebagai komponen self-contained
> dan panggil via `x-ref`:
> ```php
> echo '<div x-data>';
> echo ui_modal('myModal', 'Judul', 'Isi konten', 'Footer');
> echo '<button type="button" @click="$refs.myModal ? null : null"></button>'; // placeholder
> echo '</div>';
> ```

Untuk kasus nyata MONBIS (misal modal detail NPL), implementasi paling
sederhana: ubah modal lama (yang pakai `classList.toggle('hidden')`) menjadi
Alpine `x-data="{ open: false }"` + `x-show`, tanpa ubah isi konten/fetch JS.

### 6.5 Filter Tanggal Global

```php
// Ganti date picker manual lama dengan:
echo ui_date_range(
    [
        'closing_date' => $closing_date ?? date('Y-m-d', strtotime('last day of previous month')),
        'harian_date'  => $harian_date ?? date('Y-m-d'),
    ],
    BASE_APP . '/dashboard'
);
```

---

## 7. Roadmap Migrasi per Halaman

### Fase 0 — Foundation (Semua sekali)
| # | Task | File |
|---|------|------|
| 0.1 | Tambah Alpine + Flatpickr + CSS variables | `views/header.php`, `v-2/components/bootstrap.php` |
| 0.2 | Buat helper `ui.php` | `views/components/ui.php` (baru) |
| 0.3 | Include helper di layout | `views/header.php` |

### Fase 1 — Pilot (1 halaman bukti konsep)
| # | Halaman | Slug | Prioritas |
|---|---------|------|-----------|
| 1.1 | Executive Dashboard | `pages/dashboard.php` | **Tinggi** |

Kriteria lolos Fase 1:
- KPI box, table top/bottom, filter tanggal sudah pakai helper baru
- Semua fetch API lama tetap jalan
- Responsif di `md` & `lg`
- Di-review dulu sebelum lanjut ke fase berikutnya

### Fase 2 — Halaman Inti Pemasaran (tingkat manajemen)
| # | Halaman | Slug |
|---|---------|------|
| 2.1 | Realisasi Kredit | `realisasi_kredit` |
| 2.2 | Realisasi Kredit AO | `realisasi_ao` |
| 2.3 | Ontime Payment | `otp` |
| 2.4 | Repayment Rate | `rekap_rr` |
| 2.5 | Jatuh Tempo & Refinancing | `jatuh_tempo` |
| 2.6 | Pipelane AO Kredit | `pipelane_ao_jt` |
| 2.7 | MOB | `mob` |
| 2.8 | Migrasi Bucket SC | `migrasi_bucket_sc` |
| 2.9 | Monitoring & Evaluasi | `monev` |
| 2.10 | Dashboard V2 (Korwil) | `dashboard_v2` |

### Fase 3 — Halaman NPL & PH
| # | Halaman | Slug |
|---|---------|------|
| 3.1 | NPL | `npl` |
| 3.2 | Perbandingan NPL | `perbandingan_npl` |
| 3.3 | Recovery NPL | `recovery_npl` |
| 3.4 | Flow PAR | `flow_par` |
| 3.5 | Potensi NPL | `potensi_npl` |
| 3.6 | 25 NPL Besar | `npl_25_besar` |
| 3.7 | Flow 50 Besar | `flow_50_besar` |
| 3.8 | Recovery PH | `recovery_ph` |
| 3.9 | LGD | `lgd` |

### Fase 4 — Collection & Detail
| # | Halaman | Slug |
|---|---------|------|
| 4.1 | Migrasi Kolek | `migrasi_kolek` |
| 4.2 | Bucket DPD & Kolek | `actual_kredit` |
| 4.3 | Migrasi Bucket | `migrasi_bucket` |
| 4.4 | Search Debitur | `search_debitur` |
| 4.5 | OTP Bucket FE | `otp_bucket_fe` |
| 4.6 | Kunjungan | `kunjungan` |
| 4.7 | CKPN | `ckpn` |

### Fase 5 — Laporan, Utility & Layout
| # | Halaman | Slug |
|---|---------|------|
| 5.1 | Lapkeu Kantor | `lapkeu_kantor` |
| 5.2 | Aging Kredit | `aging_kredit` |
| 5.3 | Layanan Digital | `layanan_digital` |
| 5.4 | Profile | `profile` |
| 5.5 | Navbar & Sidebar | `views/navbar.php`, `views/components/sidebar_behavior.php` |
| 5.6 | Login | `pages/login.php` |
| 5.7 | TV / Display | `tv.php`, `tv_cabang.php` |

### Fase 6 — V2 Pages
| # | Halaman | File |
|---|---------|------|
| 6.1 | Launcher | `v-2/pages/launcher.php` |
| 6.2 | RBB | `v-2/pages/rbb.php` |
| 6.3 | KPI | `v-2/pages/kpi.php` |
| 6.4 | Collection | `v-2/pages/collection.php` |
| 6.5 | Settings & Components | `v-2/pages/settings.php`, `v-2/pages/components.php` |

> **Catatan:** V2 sudah punya component system sendiri (`v-2/components/ui.php`).
> Fase 6 = samakan token warna/radius dengan fase 1-5, **bukan** rewrite ulang.

### Backlog (tidak wajib, bisa menyusul)
- Halaman duplikat/arsip: `*copy*.php`, `*_v1.php`, `*_lama.php`, `*_ori.php`
  → kandidat **dihapus/diarsipkan**, jangan di-refactor (buang waktu).
- Dark mode toggle (setelah fase 5, CSS variables sudah siap).

---

## 8. Timeline & Milestone

> Asumsi: 1 developer, paralel dengan fitur baru.

| Minggu | Milestone | Output |
|--------|-----------|--------|
| **M1** | Foundation + Pilot | Helper `ui.php`, `dashboard.php` di-refactor, siap review |
| **M2** | Fase 2 (10 halaman inti pemasaran) | 10 halaman konsisten |
| **M3** | Fase 3 (9 halaman NPL/PH) | 9 halaman konsisten |
| **M4** | Fase 4 (7 halaman collection) | 7 halaman konsisten |
| **M5** | Fase 5 (laporan + layout + login) | Layout global rapi |
| **M6** | Fase 6 (V2 pages) + polish + dark mode | Selesai |

**Total: ~6 minggu** (≈ 35-50 jam kerja efektif).

### Milestone Review
Setelah **setiap 3 halaman selesai**, jalankan checklist [§9](#9-quality-checklist)
dan commit terpisah per halaman (`refactor(ui): dashboard` dsb).

---

## 9. Quality Checklist

### Per Halaman (wajib lolos semua)
```markdown
- [ ] Tidak ada perubahan logic fetch / query / API call
- [ ] Card memakai token shadcn (bg-card, border, shadow-sm)
- [ ] Button memakai ui_button() dengan variant yang tepat
- [ ] Badge/status memakai ui_badge() dengan tone sesuai domain
- [ ] Table memakai ui_table() / ui_th() / ui_td()
- [ ] Input/select memakai ui_input() / ui_select()
- [ ] Modal (jika ada) memakai Alpine x-show + x-transition
- [ ] Filter tanggal memakai Flatpickr
- [ ] Responsif: cek viewport 375px / 768px / 1280px
- [ ] Tidak ada file duplikat baru yang dibuat
- [ ] Tidak ada class Tailwind lama yang tertinggal (spot check)
```

### Global (sekali selesai semua fase)
- [ ] Tidak ada console error (Alpine, Flatpickr, fetch)
- [ ] Halaman login, dashboard, tv tetap bisa diakses tanpa login (sesuai whitelist lama)
- [ ] Dark mode token ready (`:root` + `.dark`)
- [ ] Navbar/sidebar konsisten di semua halaman
- [ ] Hapus/komentari file duplikat lama yang sudah di-replace (opsional, hati-hati)

---

## 10. Risiko & Mitigasi

| # | Risiko | Dampak | Mitigasi |
|---|--------|--------|----------|
| 1 | Alpine.js bentrok dengan JS inline lama (nama variabel `x-data` duplikat) | Modal/toggle tidak jalan | Scope `x-data` per wadah; jangan pakai variabel global di `x-data` |
| 2 | Tailwind class lama & baru bercampur (kotor) | Tampilan tidak konsisten | Refactor satu halaman penuh sekaligus, jangan setengah-setengah |
| 3 | Helper `ui_*()` belum termasuk di file yang tidak melewati `header.php` | Fatal error `undefined function` | Tambah `require_once` fallback di setiap halaman yang di-refactor |
| 4 | Halaman duplikat (`*copy*`) ikut di-refactor → buang waktu | Waste effort | Daftar `*copy*` / `*_lama*` = **SKIP**, masuk backlog hapus |
| 5 | Alpine CDN berubah / gagal load | Interaksi mati | Pin versi (`@3.14.1`), siapkan fallback vanilla JS untuk modal kritis |
| 6 | Flatpickr format salah → filter tanggal error | Data salah tampil | Format eksplisit `Y-m-d`, locale `id`, test di halaman pilot |
| 7 | Table panjang (NPL, rekap) performa jelek setelah styling baru | Lambat | Class hanya; pastikan tidak tambah reflow berat (hindari animasi berat di `<tr>`) |
| 8 | V2 & V1 token warna berbeda | Tampilan tidak seragam | Satu file CSS variables dipakai dua-duanya |

---

## 11. File yang Akan Dibuat/Diubah

### Baru (dibuat sekali)
```
views/components/ui.php          ← seluruh helper ui_*()
docs/rencana-refactor-ui.md      ← dokumen ini
```

### Diubah (layout global, sekali)
```
views/header.php                 ← CDN Alpine + Flatpickr + CSS vars + require ui.php
v-2/components/bootstrap.php     ← CDN Alpine + Flatpickr + CSS vars (jika V2 terpisah)
```

### Diubah (per halaman, mengikuti roadmap §7)
```
pages/dashboard.php               (Fase 1 - pilot)
pages/realisasi_kredit.php        (Fase 2)
pages/realisasi_ao.php            (Fase 2)
pages/otp.php                     (Fase 2)
pages/rekap_rr.php                (Fase 2)
pages/jatuh_tempo.php             (Fase 2)
pages/pipelane_ao_jt.php          (Fase 2)
pages/mob.php                     (Fase 2)
pages/migrasi_bucket_sc.php       (Fase 2)
pages/monev.php                   (Fase 2)
pages/dashboard_v2.php            (Fase 2)
pages/npl.php ... (lihat §7)      (Fase 3-5)
views/navbar.php                  (Fase 5)
pages/login.php                   (Fase 5)
v-2/pages/*.php                   (Fase 6)
```

### TIDAK DIUBAH
```
api/**                            ← 100% tidak disentuh
database/**                       ← 100% tidak disentuh
index.php (router)                ← tidak disentuh
api/index.php (router)            ← tidak disentuh
v-2/index.php (router)            ← tidak disentuh
pages/*copy*.php, *_v1*, *_lama*  ← backlog (kandidat hapus, jangan di-refactor)
```

---

## Lampiran A — Quick Start (Copy-Paste)

```php
<?php
// 1. views/header.php — tambahkan SEBELAH AWAL <body> atau setelah Tailwind:
?>
<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<style>/* CSS variables — lihat §4.1 */</style>
<?php
require_once __DIR__ . '/components/ui.php';
```

```php
<?php
// 2. pages/dashboard.php — contoh pilot
require_once __DIR__ . '/../views/components/ui.php';

echo ui_date_range(
    ['closing_date' => $closing_date, 'harian_date' => $harian_date],
    BASE_APP . '/dashboard'
);

echo '<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">';
echo ui_kpi_box('Saldo Bank', 'Rp ' . number_format($saldo_bank), $delta, $tone);
echo ui_kpi_box('NPL', $npl_persen . '%', $npl_delta, $npl_tone);
echo ui_kpi_box('Repayment Rate', $rr . '%', $rr_delta, $rr_tone);
echo ui_kpi_box('Realisasi', $realisasi, $rls_delta, $rls_tone);
echo '</div>';
```

---

## Lampiran B — Referensi

- shadcn/ui (design reference, **bukan** untuk di-install): https://ui.shadcn.com
- Alpine.js: https://alpinejs.dev
- Flatpickr: https://flatpickr.js.org
- Tailwind CSS: https://tailwindcss.com
- Dokumentasi lama MONBIS: `README.md`
