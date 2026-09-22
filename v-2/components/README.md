# FE Workspace components

Folder ini adalah sumber komponen bersama untuk page baru di `v-2/pages`.

- `bootstrap.php` memuat seluruh helper.
- `layout.php` menyediakan shell, sidebar, topbar, dan asset global.
- `ui.php` menyediakan button, input, select, card, modal, alert, progress, tabs, badge, dan icon button.
- `icons.php` menyimpan icon SVG inline agar tidak bergantung pada asset eksternal.

Asset visual dan interaksi berada di `v-2/assets`:

- `css/tokens.css` berisi token warna, theme, scale, dan density.
- `css/app.css` berisi layout dasar.
- `css/components.css` berisi styling component bersama.
- `js/components.js` mengatur setting, toast, tabs, dan state component.
- `js/app.js` mengatur shell, sidebar, modal, dan identitas workspace.

Page baru cukup memanggil `components/bootstrap.php`, lalu menggunakan helper `v2_*`.
