<?php
// ==========================================
// BAGIAN API PHP 
// ==========================================
$dataFile = 'tracker_data.json';

if (isset($_GET['api']) && $_GET['api'] == 'true') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (file_exists($dataFile)) {
            echo file_get_contents($dataFile);
        } else {
            echo json_encode([]);
        }
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        if (json_decode($input) !== null) {
            // CEK APAKAH BERHASIL DITULIS KE SERVER
            $result = file_put_contents($dataFile, $input);
            if ($result !== false) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500); // Server error
                echo json_encode(['status' => 'error', 'message' => 'Permission Denied! File tidak bisa ditulis.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format JSON tidak valid']);
        }
    }
    exit; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monbis Dev Tracker - BKK Jateng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 min-h-screen">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 py-3 px-4 md:py-4 md:px-6 mb-4 md:mb-6 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-bold text-blue-700 flex items-center gap-2">
                    <span class="bg-blue-600 text-white p-1 rounded text-sm md:text-base">MB</span> 
                    <span>Monbis Dev Tracker</span>
                </h1>
                <p class="text-[10px] md:text-xs text-slate-500 hidden sm:block mt-1">Live Server Sync 🟢</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2 md:gap-3">
                <button onclick="openLegendModal()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 md:py-2 rounded-lg text-xs md:text-sm font-bold transition-all border border-indigo-200 flex items-center gap-1">
                    ℹ️ <span class="hidden sm:inline">Panduan</span>
                </button>
                <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-all shadow-md shadow-blue-200 ml-2">
                    + <span class="hidden sm:inline">Tambah Menu</span>
                </button>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-3 md:px-4 pb-20">
        <div class="mb-6 md:mb-8">
            <h2 class="text-base md:text-lg font-bold text-slate-800 mb-3 md:mb-4 flex items-center justify-between gap-2">
                <span>📊 Dashboard Rekapitulasi</span>
                <span id="sync-status" class="text-xs text-slate-400 font-normal">Sinkronisasi...</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4" id="rekap-area">
                <!-- Di-render oleh JS -->
            </div>
        </div>

        <div id="content-area" class="space-y-6 md:space-y-8">
            <!-- Data rendered here -->
        </div>
    </main>

    <!-- MODAL PANDUAN PENILAIAN -->
    <div id="legend-modal" class="fixed inset-0 bg-slate-900/60 hidden backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[95vh] flex flex-col">
            <div class="p-4 md:p-6 border-b border-slate-100 flex justify-between items-center shrink-0 bg-indigo-50">
                <h2 class="text-base md:text-lg font-bold text-indigo-800 flex items-center gap-2">ℹ️ Panduan Penilaian Indikator</h2>
                <button onclick="closeLegendModal()" class="text-indigo-400 hover:text-indigo-600 p-1 font-bold text-xl">✕</button>
            </div>
            <div class="overflow-y-auto p-4 md:p-6 space-y-4 text-sm text-slate-700">
                <p class="font-medium mb-2">Setiap indikator memiliki bobot 25%. Total 100% jika semua siap.</p>
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-lg"><strong class="bg-green-100 text-green-700 border border-green-200 px-2 py-0.5 rounded mr-2 text-xs">BE READY</strong> Query Database (SQL) & API Endpoint di Backend sudah selesai dan siap dikonsumsi.</div>
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-lg"><strong class="bg-blue-100 text-blue-700 border border-blue-200 px-2 py-0.5 rounded mr-2 text-xs">FE READY</strong> Slicing UI Frontend dan proses integrasi tarik data dari Backend sudah berjalan lancar.</div>
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-lg"><strong class="bg-purple-100 text-purple-700 border border-purple-200 px-2 py-0.5 rounded mr-2 text-xs">FILTER</strong> Sistem filter data (Kode Kantor, Hierarki AO, Tanggal, dll) sudah berfungsi dengan valid.</div>
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-lg"><strong class="bg-orange-100 text-orange-700 border border-orange-200 px-2 py-0.5 rounded mr-2 text-xs">RESPONSIVE</strong> Tampilan UI aman, rapi, dan tabel tidak menumpuk saat dibuka di perangkat Mobile/HP.</div>
            </div>
            <div class="p-4 border-t border-slate-100 flex justify-end">
                <button onclick="closeLegendModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold text-sm transition-colors">Tutup Panduan</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Sub-Menu -->
    <div id="modal" class="fixed inset-0 bg-slate-900/60 hidden backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[95vh] flex flex-col">
            <div class="p-4 md:p-6 border-b border-slate-100 flex justify-between items-center shrink-0">
                <h2 class="text-base md:text-lg font-bold">Input Sub-Menu Baru</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
            </div>
            <div class="overflow-y-auto p-4 md:p-6">
                <form id="menu-form" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Menu Utama</label>
                        <input type="text" id="m_parent" placeholder="ex: Pemasaran, NPL, Laporan..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Sub-Menu</label>
                        <input type="text" id="m_name" placeholder="ex: Realisasi Kredit AO" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Dikerjakan Oleh</label>
                        <input type="text" id="m_user" value="Tim IT" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Catatan Kekurangan</label>
                        <textarea id="m_notes" rows="3" placeholder="Catatan bug atau fitur yang kurang..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-4">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium text-sm transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT CATATAN -->
    <div id="edit-modal" class="fixed inset-0 bg-slate-900/60 hidden backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[95vh] flex flex-col">
            <div class="p-4 md:p-6 border-b border-slate-100 flex justify-between items-center shrink-0">
                <h2 class="text-base md:text-lg font-bold text-blue-700">Update Catatan</h2>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
            </div>
            <div class="overflow-y-auto p-4 md:p-6">
                <form id="edit-form" class="space-y-4">
                    <input type="hidden" id="edit_id"> 
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Detail Kekurangan / Bug</label>
                        <textarea id="edit_notes" rows="6" placeholder="Ketik catatan di sini..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-4">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium text-sm transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold text-sm transition-colors">Update Server</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION REALTIME -->
    <div id="toast" class="fixed bottom-4 right-4 bg-slate-800 text-white px-4 py-3 rounded-lg shadow-2xl transform translate-y-24 opacity-0 transition-all duration-300 z-50 text-xs md:text-sm flex items-center gap-3 border border-slate-700">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
        </span>
        <span id="toast-msg" class="font-medium">Tersimpan ke Server Database!</span>
    </div>

    <!-- TOAST ERROR (BARU) -->
    <div id="toast-error" class="fixed bottom-4 right-4 bg-red-600 text-white px-4 py-3 rounded-lg shadow-2xl transform translate-y-24 opacity-0 transition-all duration-300 z-50 text-xs md:text-sm flex items-center gap-3 border border-red-700">
        <span class="font-bold text-lg">⚠️</span>
        <span id="toast-error-msg" class="font-medium">Gagal menyimpan ke Server! Cek File Permission.</span>
    </div>

    <script>
        let menuData = [];
        const API_URL = 'index.php?api=true'; 
        
        const defaultData = [
            {
                id: 1, parent: "Pemasaran", name: "Realisasi Kredit (Sample)", 
                user: "Syaifun Nadhif", notes: "Data contoh awal, testing di server dev aman.",
                be: true, fe: true, filter: true, responsif: true, progress: 100,
                created_at: new Date().toLocaleString('id-ID'), updated_at: new Date().toLocaleString('id-ID')
            }
        ];

        // --- FUNGSI AMBIL DATA DARI SERVER (GET) ---
        async function fetchFromServer() {
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                
                if (data.length === 0) {
                    menuData = defaultData;
                    saveToServer(true); 
                } else {
                    if(JSON.stringify(menuData) !== JSON.stringify(data)){
                        menuData = data;
                        renderApp();
                    }
                }
                document.getElementById('sync-status').innerHTML = '🟢 Terhubung Server';
            } catch (error) {
                console.error("Gagal konek ke server API:", error);
                document.getElementById('sync-status').innerHTML = '🔴 Server Offline';
            }
        }

        // --- FUNGSI SIMPAN DATA KE SERVER DENGAN ERROR DETECTION (POST) ---
        async function saveToServer(isSilently = false) {
            try {
                renderApp(); 
                
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(menuData)
                });

                // Cek kalau server nolak (misal permission gagal)
                if (!response.ok) {
                    throw new Error("Permission Denied Server");
                }

                if(!isSilently) showToast("Tersimpan ke Server Database!");
            } catch (error) {
                console.error("Gagal simpan ke server:", error);
                showErrorToast("Gagal simpan! Cek Permission folder/file (chmod 777)");
            }
        }

        fetchFromServer();
        setInterval(fetchFromServer, 3000); 

        // --- FUNGSI TOAST NOTIFIKASI ---
        let toastTimeout;
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').innerText = message;
            toast.classList.remove('translate-y-24', 'opacity-0');
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => { toast.classList.add('translate-y-24', 'opacity-0'); }, 3000);
        }

        let errorToastTimeout;
        function showErrorToast(message) {
            const toastError = document.getElementById('toast-error');
            document.getElementById('toast-error-msg').innerText = message;
            toastError.classList.remove('translate-y-24', 'opacity-0');
            clearTimeout(errorToastTimeout);
            errorToastTimeout = setTimeout(() => { toastError.classList.add('translate-y-24', 'opacity-0'); }, 5000);
        }

        function openLegendModal() { document.getElementById('legend-modal').classList.remove('hidden'); }
        function closeLegendModal() { document.getElementById('legend-modal').classList.add('hidden'); }
        function openModal() { document.getElementById('modal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('modal').classList.add('hidden'); document.getElementById('menu-form').reset(); }

        document.getElementById('menu-form').onsubmit = (e) => {
            e.preventDefault();
            const now = new Date().toLocaleString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'});
            const newItem = {
                id: Date.now(),
                parent: document.getElementById('m_parent').value,
                name: document.getElementById('m_name').value,
                user: document.getElementById('m_user').value,
                notes: document.getElementById('m_notes').value,
                be: false, fe: false, filter: false, responsif: false,
                progress: 0, created_at: now, updated_at: now
            };
            menuData.push(newItem);
            saveToServer();
            closeModal();
        };

        function openEditModal(id) {
            const idx = menuData.findIndex(i => i.id === id);
            if(idx !== -1) {
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_notes').value = menuData[idx].notes;
                document.getElementById('edit-modal').classList.remove('hidden');
            }
        }

        function closeEditModal() { 
            document.getElementById('edit-modal').classList.add('hidden'); 
            document.getElementById('edit-form').reset(); 
        }

        document.getElementById('edit-form').onsubmit = (e) => {
            e.preventDefault();
            const id = parseInt(document.getElementById('edit_id').value);
            const newNote = document.getElementById('edit_notes').value;
            
            const idx = menuData.findIndex(i => i.id === id);
            if(idx !== -1) {
                menuData[idx].notes = newNote;
                menuData[idx].updated_at = new Date().toLocaleString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'});
                saveToServer();
                closeEditModal();
            }
        };

        function toggleIndicator(id, type) {
            const idx = menuData.findIndex(i => i.id === id);
            menuData[idx][type] = !menuData[idx][type];
            
            let prog = 0;
            if(menuData[idx].be) prog += 25;
            if(menuData[idx].fe) prog += 25;
            if(menuData[idx].filter) prog += 25;
            if(menuData[idx].responsif) prog += 25;
            menuData[idx].progress = prog;
            
            menuData[idx].updated_at = new Date().toLocaleString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'});
            saveToServer();
        }

        function deleteItem(id) {
            const password = prompt('Masukkan password administrator untuk menghapus sub-menu ini:');
            if (password === 'oke123') {
                menuData = menuData.filter(i => i.id !== id);
                saveToServer();
            } else if (password !== null) {
                alert('Password salah! Data tidak dihapus.');
            }
        }

        function renderRekap() {
            const total = menuData.length;
            if (total === 0) {
                document.getElementById('rekap-area').innerHTML = '<p class="text-slate-500 text-sm">Belum ada data untuk direkap.</p>';
                return;
            }

            const totalProgress = menuData.reduce((sum, item) => sum + item.progress, 0);
            const avgProgress = Math.round(totalProgress / total);
            const doneCount = menuData.filter(item => item.progress === 100).length;
            const progressCount = total - doneCount;

            const beCount = menuData.filter(item => item.be).length;
            const feCount = menuData.filter(item => item.fe).length;
            const filterCount = menuData.filter(item => item.filter).length;
            const responsifCount = menuData.filter(item => item.responsif).length;

            const rekapHtml = `
                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-slate-500 font-medium mb-1">Rata-rata Progres</p>
                        <h3 class="text-2xl md:text-3xl font-black text-blue-600">${avgProgress}%</h3>
                        <p class="text-[10px] md:text-xs text-slate-400 mt-1">Dari total ${total} Sub-Menu</p>
                    </div>
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full border-[3px] md:border-4 ${avgProgress === 100 ? 'border-green-500' : 'border-blue-500'} flex items-center justify-center shrink-0">
                        <span class="text-lg md:text-xl font-bold ${avgProgress === 100 ? 'text-green-500' : 'text-blue-500'}">📊</span>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs md:text-sm text-slate-500 font-medium mb-2 md:mb-3">Status Penyelesaian</p>
                    <div class="flex gap-3 md:gap-4">
                        <div class="flex-1 bg-green-50 rounded-lg p-2 md:p-3 border border-green-100 flex flex-col justify-center">
                            <p class="text-[10px] md:text-xs text-green-600 font-bold mb-0.5">SELESAI</p>
                            <h4 class="text-xl md:text-2xl font-black text-green-700">${doneCount}</h4>
                        </div>
                        <div class="flex-1 bg-yellow-50 rounded-lg p-2 md:p-3 border border-yellow-100 flex flex-col justify-center">
                            <p class="text-[10px] md:text-xs text-yellow-600 font-bold mb-0.5">PROGRES</p>
                            <h4 class="text-xl md:text-2xl font-black text-yellow-700">${progressCount}</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs md:text-sm text-slate-500 font-medium mb-2">Kesiapan Indikator</p>
                    <div class="space-y-2">
                        <div>
                            <div class="flex justify-between items-center text-[11px] md:text-sm mb-1">
                                <span class="text-slate-600">Backend (BE)</span>
                                <span class="font-bold text-slate-800">${beCount}/${total}</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-green-500 h-full" style="width: ${(beCount/total)*100}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-center text-[11px] md:text-sm mb-1">
                                <span class="text-slate-600">Frontend (FE)</span>
                                <span class="font-bold text-slate-800">${feCount}/${total}</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full" style="width: ${(feCount/total)*100}%"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center text-[11px] md:text-sm pt-1">
                            <span class="text-slate-600">Filter & Responsif</span>
                            <span class="font-bold text-slate-800">${filterCount}/${total} & ${responsifCount}/${total}</span>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('rekap-area').innerHTML = rekapHtml;
        }

        function renderApp() {
            renderRekap(); 

            const container = document.getElementById('content-area');
            container.innerHTML = '';

            const grouped = menuData.reduce((acc, item) => {
                if (!acc[item.parent]) acc[item.parent] = [];
                acc[item.parent].push(item);
                return acc;
            }, {});

            for (const parent in grouped) {
                const section = document.createElement('div');
                section.className = "bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden";
                
                let html = `
                    <div class="bg-slate-50 border-b border-slate-200 p-3 md:p-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div class="w-1.5 h-5 md:w-2 md:h-6 bg-blue-600 rounded-full"></div>
                            <h2 class="text-base md:text-lg font-bold text-slate-800">${parent}</h2>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-[10px] md:text-xs px-2 py-1 md:px-3 rounded-full font-bold">${grouped[parent].length} Item</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                `;

                grouped[parent].forEach(item => {
                    html += `
                        <div class="p-4 md:p-5 flex flex-col lg:flex-row gap-4 md:gap-6 items-start lg:items-center hover:bg-slate-50/50 transition-colors">
                            
                            <div class="flex-1 w-full">
                                <div class="flex items-center gap-2 mb-1 md:mb-2">
                                    <h4 class="text-base md:text-lg font-bold text-slate-800 leading-tight">${item.name}</h4>
                                </div>
                                
                                <details class="mb-2 md:mb-3 cursor-pointer group">
                                    <summary class="text-[11px] md:text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 focus:outline-none w-fit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4 group-open:rotate-90 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Lihat Catatan
                                    </summary>
                                    
                                    <div class="mt-2 text-xs md:text-sm text-slate-700 bg-yellow-50 p-2 md:p-3 rounded-lg border border-yellow-100 shadow-inner relative group/note">
                                        <div class="pr-8 whitespace-pre-line">${item.notes || 'Tidak ada catatan.'}</div>
                                        <button onclick="openEditModal(${item.id})" class="absolute top-2 right-2 p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-all opacity-70 hover:opacity-100" title="Edit Catatan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    </div>
                                </details>

                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-[10px] md:text-[11px] text-slate-500 font-medium">
                                    <span>🕒 ${item.created_at}</span>
                                    <span class="text-blue-600">🔄 ${item.updated_at}</span>
                                    <span class="bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">👤 ${item.user}</span>
                                </div>
                            </div>
                            
                            <div class="w-full lg:w-auto flex flex-col sm:flex-row lg:flex-row gap-4 lg:gap-6 items-start sm:items-center">
                                <div class="grid grid-cols-2 md:flex md:flex-wrap gap-2 w-full sm:w-auto shrink-0">
                                    <button onclick="toggleIndicator(${item.id}, 'be')" class="w-full md:w-auto px-2 md:px-3 py-1.5 md:py-2 rounded-lg text-[10px] md:text-xs font-bold border md:border-2 transition-all ${item.be ? 'bg-green-100 border-green-500 text-green-700' : 'border-slate-200 text-slate-400 hover:border-slate-300'}">
                                        ${item.be ? '✓ BE READY' : 'BE WAIT'}
                                    </button>
                                    <button onclick="toggleIndicator(${item.id}, 'fe')" class="w-full md:w-auto px-2 md:px-3 py-1.5 md:py-2 rounded-lg text-[10px] md:text-xs font-bold border md:border-2 transition-all ${item.fe ? 'bg-blue-100 border-blue-500 text-blue-700' : 'border-slate-200 text-slate-400 hover:border-slate-300'}">
                                        ${item.fe ? '✓ FE READY' : 'FE WAIT'}
                                    </button>
                                    <button onclick="toggleIndicator(${item.id}, 'filter')" class="w-full md:w-auto px-2 md:px-3 py-1.5 md:py-2 rounded-lg text-[10px] md:text-xs font-bold border md:border-2 transition-all ${item.filter ? 'bg-purple-100 border-purple-500 text-purple-700' : 'border-slate-200 text-slate-400 hover:border-slate-300'}">
                                        ${item.filter ? '✓ FILTER' : 'NO FILTER'}
                                    </button>
                                    <button onclick="toggleIndicator(${item.id}, 'responsif')" class="w-full md:w-auto px-2 md:px-3 py-1.5 md:py-2 rounded-lg text-[10px] md:text-xs font-bold border md:border-2 transition-all ${item.responsif ? 'bg-orange-100 border-orange-500 text-orange-700' : 'border-slate-200 text-slate-400 hover:border-slate-300'}">
                                        ${item.responsif ? '✓ RESPONSIVE' : 'NO RESPONSIVE'}
                                    </button>
                                </div>
                                
                                <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto pt-3 sm:pt-0 border-t border-slate-100 sm:border-none shrink-0">
                                    <div class="flex flex-row sm:flex-col items-center sm:justify-center gap-3 sm:gap-0 w-full sm:w-[80px]">
                                        <span class="text-xl md:text-2xl font-black ${item.progress === 100 ? 'text-green-500' : 'text-slate-700'}">${item.progress}%</span>
                                        <div class="flex-1 sm:w-full h-1.5 bg-slate-200 rounded-full sm:mt-1 overflow-hidden">
                                            <div class="h-full ${item.progress === 100 ? 'bg-green-500' : 'bg-blue-500'} transition-all duration-300" style="width: ${item.progress}%"></div>
                                        </div>
                                    </div>

                                    <button onclick="deleteItem(${item.id})" class="p-1.5 md:p-2 text-slate-400 hover:text-white hover:bg-red-500 rounded-lg transition-colors border border-slate-200 hover:border-red-500 shrink-0" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                section.innerHTML = html;
                container.appendChild(section);
            }
        }
    </script>
</body>
</html>