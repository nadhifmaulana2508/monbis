<style>
  /* --- CUSTOM SCROLLBAR --- */
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }

  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* --- MAGIC STICKY TABLE UTAMA (COMPACT & FREEZE) --- */
  #tabelUtama { border-collapse: separate; border-spacing: 0; }
  #tabelUtama th, #tabelUtama td { background-clip: padding-box; background-color: #fff; }
  
  /* Header Lapis 1 & 2 */
  .head-lapis-1 th { height: 36px; background-color: #f1f5f9 !important; color: #1e3a8a; font-weight: 800; position: sticky !important; top: 0 !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1, inset 0 1px 0 #cbd5e1;}
  .head-lapis-2 th { top: 36px !important; height: 30px; background-color: #f8fafc !important; color: #334155; position: sticky !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1;}
  
  /* Kolom Freeze Kiri (Header) */
  .head-lapis-1 th.freeze-col-1 { z-index: 60 !important; left: 0 !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e0f2fe !important; border-top-left-radius: 8px; } 
  .head-lapis-1 th.freeze-col-2 { z-index: 59 !important; left: 0 !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e0f2fe !important; } 
  
  /* Lapis 3 (Grand Total) - Freeze di bawah Header */
  .mob-row-tot th { top: 66px !important; z-index: 45 !important; height: 38px; box-shadow: inset 0 -2px 0 #93c5fd; background-color: #eff6ff !important; cursor: default; position: sticky !important;}
  .mob-row-tot th.freeze-col-1 { z-index: 62 !important; left: 0 !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }
  .mob-row-tot th.freeze-col-2 { z-index: 61 !important; left: 0 !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }

  @media (min-width: 768px) {
      .head-lapis-1 th { height: 40px; }
      .head-lapis-2 th { top: 40px !important; height: 34px; }
      .head-lapis-1 th.freeze-col-2 { left: 48px !important; }
      .mob-row-tot th { top: 74px !important; height: 42px; }
      .mob-row-tot th.freeze-col-2 { left: 48px !important; }
  }

  /* Freeze Kiri (Body) */
  #bodyUtama td { position: relative; z-index: 10 !important; }
  #bodyUtama td.freeze-col-1 { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  #bodyUtama td.freeze-col-2 { position: sticky !important; left: 0 !important; z-index: 29 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  @media (min-width: 768px) { #bodyUtama td.freeze-col-2 { left: 48px !important; } }

  /* Hover Effects */
  .cell-hover:hover { background-color: #e0f2fe !important; cursor: pointer; transform: scale(1.05); transition: 0.1s; z-index: 35 !important; position: relative; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #3b82f6; border-radius: 6px; }
  #bodyUtama tr:hover td { background-color: #f8fafc !important; }
  #bodyUtama tr:hover td.freeze-col-1, #bodyUtama tr:hover td.freeze-col-2 { filter: brightness(0.98); background-color: #f8fafc !important;}

  /* --- STYLING UNTUK FORM FILTER --- */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; font-size:10px; background:#fff; outline:none; transition: border 0.2s; width: 100%; height: 32px;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f8fafc; color: #94a3b8; cursor: not-allowed; border-color: #e2e8f0; }
  select.inp { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em; padding-right: 1.5rem; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:10px; } .inp { border-radius: 8px; padding:0 10px; font-size:12px; height: 36px;} select.inp { padding-right: 2rem; } }
  .field { display:flex; flex-direction:column; min-width: 0; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s; height: 32px; border-radius: 6px;}
  @media (min-width: 768px) { .btn-icon { height: 36px; border-radius: 8px; } }
  .btn-icon:hover:not(:disabled) { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }
</style>
