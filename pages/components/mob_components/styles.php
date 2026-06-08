<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* Searchable Dropdown Custom Styles */
  .search-dropdown-list { display: none; position: absolute; z-index: 100; width: 100%; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #cbd5e1; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 4px; }
  .search-dropdown-list.show { display: block; }
  .dropdown-item { padding: 8px 12px; font-size: 11px; cursor: pointer; transition: background 0.1s; border-bottom: 1px solid #f1f5f9; }
  .dropdown-item:hover { background: #eff6ff; color: #1d4ed8; font-weight: 600; }

  /* ========================================================
     🔥 MAGIC STICKY TABLE UTAMA (MOB) 🔥
     ======================================================== */
  #tabelMob { border-collapse: separate; border-spacing: 0; }
  #tabelMob th, #tabelMob td { background-clip: padding-box; background-color: #fff; }
  
  /* Thead Consistency */
  #tabelMob thead th { position: sticky !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1, inset 0 1px 0 #cbd5e1; }
  
  /* Lapis 1 & 2 (Header Utama) - Dibuat Seragam */
  .mob-row-1 th { top: 0 !important; height: 40px; background-color: #f1f5f9 !important; color: #334155; }
  .mob-row-2 th { top: 40px !important; height: 34px; background-color: #f8fafc !important; color: #475569; }
  
  /* Freeze Kolom Kiri Header Utama */
  .mob-row-1 th.sticky-left { z-index: 60 !important; left: 0 !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e2e8f0 !important; border-top-left-radius: 8px; } 
  
  /* Lapis 3 (Grand Total) */
  .mob-row-tot th { top: 74px !important; z-index: 45 !important; height: 42px; box-shadow: inset 0 -2px 0 #93c5fd; background-color: #eff6ff !important; cursor: default; }
  .mob-row-tot th.sticky-left { z-index: 62 !important; left: 0 !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }

  @media (min-width: 768px) {
      .mob-row-1 th { height: 46px; }
      .mob-row-2 th { top: 46px !important; height: 38px; }
      .mob-row-tot th { top: 84px !important; height: 50px; }
  }

  /* Freeze Kiri Body Utama */
  #bodyMatrix td { position: relative; z-index: 10 !important; }
  .sticky-left { position: sticky !important; left: 0 !important; }
  #bodyMatrix td.sticky-left { z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  
  /* Hover Effects Utama */
  .cell-hover:hover { background-color: #e0f2fe !important; cursor: pointer; transform: scale(1.03); transition: 0.1s; z-index: 35 !important; position: relative; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #3b82f6; border-radius: 6px; }
  #bodyMatrix tr:hover td { background-color: #f8fafc !important; }
  #bodyMatrix tr:hover td.sticky-left { background-color: #f8fafc !important; filter: brightness(0.98); }

  /* ========================================================
     🔥 TABEL MODAL DETAIL MOB 🔥
     ======================================================== */
  #tableExportMob { border-collapse: separate; border-spacing: 0; }
  #tableExportMob th, #tableExportMob td { background-clip: padding-box; background-color: #fff; }
  
  #tableExportMob thead th { height: 46px; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1, 0 1px 0 #cbd5e1; top: 0 !important; position: sticky !important; z-index: 40 !important; color: #475569; }
  @media (min-width: 768px) { #tableExportMob thead th { height: 48px; } }

  #bodyModalDetail td { position: relative; z-index: 10; }

  #tableExportMob th.mod-td-rek, #bodyModalDetail td.mod-td-rek { position: sticky !important; left: 0 !important; z-index: 31 !important; background-color: #fff !important; box-shadow: inset -1px 0 0 #e2e8f0; min-width: 100px; max-width: 100px; }
  #tableExportMob th.mod-td-nas, #bodyModalDetail td.mod-td-nas { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #fff !important; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); min-width: 160px; max-width: 160px; }
  
  @media (min-width: 768px) { 
      #tableExportMob th.mod-td-rek, #bodyModalDetail td.mod-td-rek { min-width: 120px; max-width: 120px; }
      #tableExportMob th.mod-td-nas, #bodyModalDetail td.mod-td-nas { left: 120px !important; min-width: 250px; max-width: 250px; } 
  }

  #tableExportMob thead th.mod-td-rek, #tableExportMob thead th.mod-td-nas { z-index: 60 !important; background-color: #e2e8f0 !important; }

  #bodyModalDetail tr:hover td { background-color: #f8fafc !important; }
  #bodyModalDetail tr:hover td.mod-td-rek, #bodyModalDetail tr:hover td.mod-td-nas { filter: brightness(0.98); background-color: #f8fafc !important; }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; font-weight: 700; cursor: not-allowed; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:11px; margin-bottom:4px; } .inp { border-radius: 8px; padding:0 12px; } }
  .field { display:flex; flex-direction:column; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
</style>