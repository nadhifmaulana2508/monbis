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

  /* ========================================================
     🔥 MAGIC STICKY TABLE UTAMA (COMPACT) 🔥
     ======================================================== */
  #tabelMob { border-collapse: separate; border-spacing: 0; }
  #tabelMob th, #tabelMob td { background-clip: padding-box; background-color: #fff; }
  
  #tabelMob thead th { position: sticky !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1, inset 0 1px 0 #cbd5e1; }
  
  .mob-row-1 th { top: 0 !important; height: 36px; background-color: #f1f5f9 !important; color: #1e3a8a; font-weight: 800; }
  .mob-row-2 th { top: 36px !important; height: 30px; background-color: #f8fafc !important; color: #334155; }
  .mob-row-1 th.sticky-left { z-index: 60 !important; left: 0 !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e0f2fe !important; border-top-left-radius: 8px; } 
  
  .mob-row-tot th { top: 66px !important; z-index: 45 !important; height: 38px; box-shadow: inset 0 -2px 0 #93c5fd; background-color: #eff6ff !important; cursor: default; }
  .mob-row-tot th.sticky-left { z-index: 62 !important; left: 0 !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }

  @media (min-width: 768px) {
      .mob-row-1 th { height: 40px; }
      .mob-row-2 th { top: 40px !important; height: 34px; }
      .mob-row-tot th { top: 74px !important; height: 42px; }
  }

  #bodyMatrix td { position: relative; z-index: 10 !important; }
  .sticky-left { position: sticky !important; left: 0 !important; }
  #bodyMatrix td.sticky-left { z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  
  .cell-hover:hover { background-color: #e0f2fe !important; cursor: pointer; transform: scale(1.05); transition: 0.1s; z-index: 35 !important; position: relative; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #3b82f6; border-radius: 6px; }
  #bodyMatrix tr:hover td { background-color: #f8fafc !important; }
  #bodyMatrix tr:hover td.sticky-left { background-color: #f8fafc !important; filter: brightness(0.98); }

  /* ========================================================
     🔥 TABEL MODAL DETAIL MOB 🔥
     ======================================================== */
  #tableExportMob { border-collapse: separate; border-spacing: 0; }
  #tableExportMob th, #tableExportMob td { background-clip: padding-box; background-color: #fff; }
  
  #tableExportMob thead th { height: 40px; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1, 0 1px 0 #cbd5e1; top: 0 !important; position: sticky !important; z-index: 40 !important; color: #475569; }

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
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s; width: 100%;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f8fafc; color: #94a3b8; cursor: not-allowed; border-color: #e2e8f0; }
  select.inp { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em; padding-right: 1.8rem; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:10px; } .inp { border-radius: 8px; padding:0 10px; } select.inp { padding-right: 2rem; } }
  .field { display:flex; flex-direction:column; min-width: 0; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover:not(:disabled) { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }
</style>