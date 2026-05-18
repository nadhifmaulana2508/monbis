<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monbis - Dev Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; }
        .progress-bar { transition: width 0.5s ease; }
        .modal-backdrop { background: rgba(15,23,42,0.6); backdrop-filter: blur(2px); }
        .tab-active { border-color: #3b82f6; color: #1e40af; background: #eff6ff; }
        .status-done { background: #dcfce7; color: #166534; }
        .status-in_progress { background: #fef9c3; color: #854d0e; }
        .status-backlog { background: #f1f5f9; color: #475569; }
        .status-blocked { background: #fee2e2; color: #991b1b; }
        .status-deprecated { background: #f3e8ff; color: #6b21a8; }
        .pri-critical { background: #fef2f2; border-left: 3px solid #dc2626; }
        .pri-high { background: #fffbeb; border-left: 3px solid #f59e0b; }
        .pri-medium { background: #f0fdf4; border-left: 3px solid #22c55e; }
        .pri-low { background: #f8fafc; border-left: 3px solid #94a3b8; }
        .toast { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body class="min-h-screen">

<!-- TOAST NOTIFICATION -->
<div id="toastContainer" class="fixed top-4 right-4 z-[9999] space-y-2"></div>

<!-- HEADER -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Monbis Dev Tracker</h1>
                <p class="text-xs text-slate-500">Tracking progress pengerjaan fitur</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModal('idea')" class="px-3 py-2 text-sm bg-amber-50 text-amber-700 border border-amber-200 rounded-lg hover:bg-amber-100 font-medium">+ Ide Baru</button>
            <button onclick="openModal('feature')" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">+ Fitur Baru</button>
        </div>
    </div>
</header>

<!-- SUMMARY CARDS -->
<section id="summarySection" class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="summaryCards">
        <div class="bg-white rounded-xl border p-4 text-center"><div class="text-2xl font-bold text-slate-800" id="cardTotal">-</div><div class="text-xs text-slate-500 mt-1">Total Fitur</div></div>
        <div class="bg-white rounded-xl border p-4 text-center"><div class="text-2xl font-bold text-green-600" id="cardDone">-</div><div class="text-xs text-slate-500 mt-1">Selesai</div></div>
        <div class="bg-white rounded-xl border p-4 text-center"><div class="text-2xl font-bold text-yellow-600" id="cardProgress">-</div><div class="text-xs text-slate-500 mt-1">In Progress</div></div>
        <div class="bg-white rounded-xl border p-4 text-center"><div class="text-2xl font-bold text-slate-500" id="cardBacklog">-</div><div class="text-xs text-slate-500 mt-1">Backlog</div></div>
        <div class="bg-white rounded-xl border p-4 text-center"><div class="text-2xl font-bold text-blue-600" id="cardAvg">-</div><div class="text-xs text-slate-500 mt-1">Avg Progress</div></div>
    </div>
</section>

<!-- TABS -->
<section class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex gap-1 border-b border-slate-200">
        <button onclick="switchTab('overview')" id="tabOverview" class="px-4 py-2.5 text-sm font-medium border-b-2 tab-active rounded-t-lg">Overview</button>
        <button onclick="switchTab('features')" id="tabFeatures" class="px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 rounded-t-lg">Semua Fitur</button>
        <button onclick="switchTab('logs')" id="tabLogs" class="px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 rounded-t-lg">Activity Log</button>
        <button onclick="switchTab('ideas')" id="tabIdeas" class="px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 rounded-t-lg">Backlog Ide</button>
    </div>
</section>

<!-- TAB CONTENT -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
    <!-- OVERVIEW TAB -->
    <div id="contentOverview" class="space-y-4"></div>
    <!-- FEATURES TAB -->
    <div id="contentFeatures" class="hidden space-y-3"></div>
    <!-- LOGS TAB -->
    <div id="contentLogs" class="hidden"></div>
    <!-- IDEAS TAB -->
    <div id="contentIdeas" class="hidden space-y-3"></div>
</section>

<!-- MODAL FEATURE -->
<div id="modalFeature" class="fixed inset-0 z-[9000] hidden">
    <div class="modal-backdrop absolute inset-0" onclick="closeModal('feature')"></div>
    <div class="absolute inset-4 md:inset-auto md:top-[5%] md:left-1/2 md:-translate-x-1/2 md:w-[600px] bg-white rounded-2xl shadow-2xl overflow-y-auto max-h-[90vh] z-10">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
            <h2 id="modalFeatureTitle" class="text-lg font-bold text-slate-800">Tambah Fitur Baru</h2>
            <button onclick="closeModal('feature')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formFeature" onsubmit="submitFeature(event)" class="p-6 space-y-4">
            <input type="hidden" id="featureId" value="">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-medium text-slate-600">Module *</label><select id="fModule" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" required></select></div>
                <div><label class="text-xs font-medium text-slate-600">Kode Fitur *</label><input id="fKode" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="REALISASI_KREDIT" required></div>
            </div>
            <div><label class="text-xs font-medium text-slate-600">Nama Fitur *</label><input id="fNama" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="Realisasi Kredit" required></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-medium text-slate-600">Slug (URL)</label><input id="fSlug" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="realisasi_kredit"></div>
                <div><label class="text-xs font-medium text-slate-600">Prioritas</label><select id="fPrioritas" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm"><option value="critical">Critical</option><option value="high">High</option><option value="medium" selected>Medium</option><option value="low">Low</option></select></div>
            </div>
            <div><label class="text-xs font-medium text-slate-600">Deskripsi</label><textarea id="fDeskripsi" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" rows="2" placeholder="Penjelasan singkat fitur..."></textarea></div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="text-xs font-medium text-slate-600">Status</label><select id="fStatus" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm"><option value="backlog">Backlog</option><option value="in_progress">In Progress</option><option value="done">Done</option><option value="blocked">Blocked</option></select></div>
                <div><label class="text-xs font-medium text-slate-600">Progress %</label><input id="fProgress" type="number" min="0" max="100" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" value="0"></div>
                <div><label class="text-xs font-medium text-slate-600">Assignee</label><input id="fAssignee" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="Nadhif"></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="text-xs font-medium text-slate-600">File Page</label><input id="fFilePage" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="pages/xxx.php"></div>
                <div><label class="text-xs font-medium text-slate-600">File Controller</label><input id="fFileCtrl" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="api/controllers/Xxx.php"></div>
                <div><label class="text-xs font-medium text-slate-600">File Route</label><input id="fFileRoute" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="api/routes/xxx.php"></div>
            </div>
            <div><label class="text-xs font-medium text-slate-600">Catatan (untuk log)</label><input id="fCatatan" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="Apa yang dikerjakan..."></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="closeModal('feature')" class="px-6 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IDEA -->
<div id="modalIdea" class="fixed inset-0 z-[9000] hidden">
    <div class="modal-backdrop absolute inset-0" onclick="closeModal('idea')"></div>
    <div class="absolute inset-4 md:inset-auto md:top-[10%] md:left-1/2 md:-translate-x-1/2 md:w-[500px] bg-white rounded-2xl shadow-2xl overflow-y-auto max-h-[80vh] z-10">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
            <h2 id="modalIdeaTitle" class="text-lg font-bold text-slate-800">Tambah Ide Fitur</h2>
            <button onclick="closeModal('idea')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formIdea" onsubmit="submitIdea(event)" class="p-6 space-y-4">
            <input type="hidden" id="ideaId" value="">
            <div><label class="text-xs font-medium text-slate-600">Judul *</label><input id="iJudul" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" required placeholder="Export PDF Laporan NPL"></div>
            <div><label class="text-xs font-medium text-slate-600">Deskripsi</label><textarea id="iDeskripsi" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" rows="3" placeholder="Detail ide fitur..."></textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-medium text-slate-600">Module</label><select id="iModule" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm"><option value="">-- Belum ditentukan --</option></select></div>
                <div><label class="text-xs font-medium text-slate-600">Prioritas</label><select id="iPrioritas" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm"><option value="critical">Critical</option><option value="high">High</option><option value="medium" selected>Medium</option><option value="low">Low</option></select></div>
            </div>
            <div><label class="text-xs font-medium text-slate-600">Diusulkan Oleh</label><input id="iOleh" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" placeholder="Nadhif"></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600">Simpan Ide</button>
                <button type="button" onclick="closeModal('idea')" class="px-6 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL LOG (Quick Update Progress) -->
<div id="modalLog" class="fixed inset-0 z-[9000] hidden">
    <div class="modal-backdrop absolute inset-0" onclick="closeModal('log')"></div>
    <div class="absolute inset-4 md:inset-auto md:top-[15%] md:left-1/2 md:-translate-x-1/2 md:w-[450px] bg-white rounded-2xl shadow-2xl z-10">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Update Progress</h2>
            <button onclick="closeModal('log')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formLog" onsubmit="submitLog(event)" class="p-6 space-y-4">
            <input type="hidden" id="logFeatureId" value="">
            <div class="p-3 bg-slate-50 rounded-lg"><span class="text-xs text-slate-500">Fitur:</span><div id="logFeatureName" class="font-medium text-slate-800 text-sm"></div></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-xs font-medium text-slate-600">Status Baru</label><select id="logStatus" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm"><option value="backlog">Backlog</option><option value="in_progress">In Progress</option><option value="done">Done</option><option value="blocked">Blocked</option></select></div>
                <div><label class="text-xs font-medium text-slate-600">Progress %</label><input id="logProgress" type="number" min="0" max="100" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm"></div>
            </div>
            <div><label class="text-xs font-medium text-slate-600">Catatan *</label><textarea id="logCatatan" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" rows="3" placeholder="Apa yang dikerjakan session ini..." required></textarea></div>
            <div><label class="text-xs font-medium text-slate-600">Dikerjakan Oleh</label><input id="logOleh" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm" value="Nadhif"></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Simpan Log</button>
                <button type="button" onclick="closeModal('log')" class="px-6 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
// ============================================================
// CONFIG & STATE
// ============================================================
const API = '../api/?request=dev_tracking';
let STATE = { modules: [], features: [], summary: null, currentTab: 'overview' };

async function api(type, data = {}) {
    const res = await fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, ...data })
    });
    return await res.json();
}

function toast(msg, type = 'success') {
    const c = document.getElementById('toastContainer');
    const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
    const el = document.createElement('div');
    el.className = `toast px-4 py-3 rounded-lg text-white text-sm shadow-lg ${colors[type] || colors.info}`;
    el.textContent = msg;
    c.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => { loadAll(); });

async function loadAll() {
    const [sumRes, featRes] = await Promise.all([
        api('summary'),
        api('get_features')
    ]);

    if (sumRes.status === 200) {
        STATE.summary = sumRes.data;
        STATE.modules = sumRes.data.modules;
        renderSummary();
        renderOverview();
        populateModuleDropdowns();
    }
    if (featRes.status === 200) {
        STATE.features = featRes.data;
        renderFeatures();
    }
}

// ============================================================
// RENDER SUMMARY
// ============================================================
function renderSummary() {
    const g = STATE.summary.grand_total;
    document.getElementById('cardTotal').textContent = g.total || 0;
    document.getElementById('cardDone').textContent = g.done || 0;
    document.getElementById('cardProgress').textContent = g.in_progress || 0;
    document.getElementById('cardBacklog').textContent = g.backlog || 0;
    document.getElementById('cardAvg').textContent = (g.avg_progress || 0) + '%';
}

// ============================================================
// RENDER OVERVIEW (Per Module Cards)
// ============================================================
function renderOverview() {
    const container = document.getElementById('contentOverview');
    const modules = STATE.summary.modules;
    const logs = STATE.summary.recent_logs;

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
    modules.forEach(m => {
        const pct = m.avg_progress || 0;
        const barColor = pct >= 90 ? 'bg-green-500' : pct >= 60 ? 'bg-blue-500' : pct >= 30 ? 'bg-yellow-500' : 'bg-slate-400';
        html += `
        <div class="bg-white rounded-xl border p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-800">${m.nama_module}</h3>
                <span class="text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded-full">${m.kode_module}</span>
            </div>
            <div class="flex gap-4 text-xs mb-3">
                <span class="text-green-600 font-medium">${m.done || 0} done</span>
                <span class="text-yellow-600 font-medium">${m.in_progress || 0} progress</span>
                <span class="text-slate-400">${m.backlog || 0} backlog</span>
            </div>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full ${barColor} progress-bar rounded-full" style="width: ${pct}%"></div>
            </div>
            <div class="text-right text-xs text-slate-500 mt-1">${pct}% avg</div>
        </div>`;
    });
    html += '</div>';

    // Recent Activity
    if (logs && logs.length > 0) {
        html += '<div class="mt-6"><h3 class="text-sm font-bold text-slate-700 mb-3">Activity Terakhir</h3><div class="bg-white rounded-xl border divide-y">';
        logs.forEach(l => {
            const badge = l.status_sesudah ? `<span class="text-[10px] px-1.5 py-0.5 rounded status-${l.status_sesudah}">${l.status_sesudah}</span>` : '';
            html += `<div class="px-4 py-3 flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-slate-800 truncate"><span class="font-medium">${l.nama_fitur}</span> — ${l.catatan || '-'}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">${l.dikerjakan_oleh || '-'} · ${formatDate(l.created_at)}</div>
                </div>
                ${badge}
            </div>`;
        });
        html += '</div></div>';
    }

    container.innerHTML = html;
}

// ============================================================
// RENDER FEATURES TABLE
// ============================================================
function renderFeatures() {
    const container = document.getElementById('contentFeatures');
    const features = STATE.features;

    if (!features || features.length === 0) {
        container.innerHTML = '<div class="text-center py-12 text-slate-400">Belum ada fitur. Klik "+ Fitur Baru" untuk menambahkan.</div>';
        return;
    }

    // Group by module
    const grouped = {};
    features.forEach(f => {
        const key = f.nama_module || 'Uncategorized';
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(f);
    });

    let html = '';
    // Filter toolbar
    html += `<div class="flex flex-wrap gap-2 mb-4">
        <button onclick="filterFeatures('')" class="px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-slate-50 font-medium">Semua</button>
        <button onclick="filterFeatures('done')" class="px-3 py-1.5 text-xs rounded-lg border bg-green-50 text-green-700 hover:bg-green-100 font-medium">Done</button>
        <button onclick="filterFeatures('in_progress')" class="px-3 py-1.5 text-xs rounded-lg border bg-yellow-50 text-yellow-700 hover:bg-yellow-100 font-medium">In Progress</button>
        <button onclick="filterFeatures('backlog')" class="px-3 py-1.5 text-xs rounded-lg border bg-slate-50 text-slate-600 hover:bg-slate-100 font-medium">Backlog</button>
        <button onclick="filterFeatures('blocked')" class="px-3 py-1.5 text-xs rounded-lg border bg-red-50 text-red-700 hover:bg-red-100 font-medium">Blocked</button>
    </div>`;

    Object.keys(grouped).forEach(moduleName => {
        html += `<div class="mb-4"><h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 px-1">${moduleName}</h3><div class="space-y-2">`;
        grouped[moduleName].forEach(f => {
            const pct = f.progress_persen || 0;
            const barColor = pct >= 90 ? 'bg-green-500' : pct >= 60 ? 'bg-blue-500' : pct >= 30 ? 'bg-yellow-500' : 'bg-slate-300';
            html += `
            <div class="bg-white rounded-lg border p-4 pri-${f.prioritas} hover:shadow-sm transition-shadow feature-card" data-status="${f.status}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-sm text-slate-800">${f.nama_fitur}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full status-${f.status}">${f.status.replace('_',' ')}</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate">${f.deskripsi || '-'}</p>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 max-w-[200px] h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full ${barColor} progress-bar rounded-full" style="width:${pct}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-500 font-medium">${pct}%</span>
                            <span class="text-[10px] text-slate-400">${f.slug || ''}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button onclick="openLogModal(${f.id}, '${escape(f.nama_fitur)}', '${f.status}', ${pct})" class="p-1.5 rounded-md hover:bg-green-50 text-green-600" title="Update Progress">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button onclick="editFeature(${f.id})" class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button onclick="deleteFeature(${f.id}, '${escape(f.nama_fitur)}')" class="p-1.5 rounded-md hover:bg-red-50 text-red-500" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>`;
        });
        html += '</div></div>';
    });

    container.innerHTML = html;
}

function filterFeatures(status) {
    document.querySelectorAll('.feature-card').forEach(card => {
        if (!status || card.dataset.status === status) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ============================================================
// RENDER LOGS
// ============================================================
async function renderLogs() {
    const container = document.getElementById('contentLogs');
    container.innerHTML = '<div class="text-center py-8 text-slate-400">Loading...</div>';

    const res = await api('get_logs', { limit: 50 });
    if (res.status !== 200) { container.innerHTML = '<div class="text-red-500">Error loading logs</div>'; return; }

    const logs = res.data;
    if (!logs || logs.length === 0) { container.innerHTML = '<div class="text-center py-12 text-slate-400">Belum ada activity log.</div>'; return; }

    let html = '<div class="bg-white rounded-xl border divide-y">';
    logs.forEach(l => {
        const badge = l.status_sesudah ? `<span class="text-[10px] px-1.5 py-0.5 rounded status-${l.status_sesudah}">${l.status_sesudah}</span>` : '';
        const progressInfo = (l.progress_sebelum !== null && l.progress_sesudah !== null) ? `<span class="text-[10px] text-slate-400 ml-2">${l.progress_sebelum}% → ${l.progress_sesudah}%</span>` : '';
        html += `<div class="px-5 py-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-sm font-medium text-slate-800">${l.nama_fitur}</span>
                ${badge}${progressInfo}
            </div>
            <p class="text-sm text-slate-600">${l.catatan || '-'}</p>
            <div class="text-[10px] text-slate-400 mt-1">${l.dikerjakan_oleh || '-'} · ${formatDate(l.created_at)}${l.session_id ? ' · Session: ' + l.session_id.substring(0,8) : ''}</div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

// ============================================================
// RENDER IDEAS
// ============================================================
async function renderIdeas() {
    const container = document.getElementById('contentIdeas');
    container.innerHTML = '<div class="text-center py-8 text-slate-400">Loading...</div>';

    const res = await api('get_ideas');
    if (res.status !== 200) { container.innerHTML = '<div class="text-red-500">Error</div>'; return; }

    const ideas = res.data;
    if (!ideas || ideas.length === 0) { container.innerHTML = '<div class="text-center py-12 text-slate-400">Belum ada ide. Klik "+ Ide Baru" untuk menambahkan.</div>'; return; }

    let html = '';
    ideas.forEach(i => {
        const statusColors = { idea: 'bg-purple-100 text-purple-700', approved: 'bg-green-100 text-green-700', rejected: 'bg-red-100 text-red-700', merged: 'bg-blue-100 text-blue-700' };
        const sc = statusColors[i.status] || statusColors.idea;
        html += `
        <div class="bg-white rounded-lg border p-4 pri-${i.prioritas}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-medium text-sm text-slate-800">${i.judul}</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full ${sc}">${i.status}</span>
                    </div>
                    <p class="text-xs text-slate-500">${i.deskripsi || '-'}</p>
                    <div class="text-[10px] text-slate-400 mt-1">${i.nama_module || 'Belum ditentukan'} · ${i.diusulkan_oleh || '-'} · ${formatDate(i.created_at)}</div>
                </div>
                <div class="flex gap-1 shrink-0">
                    <button onclick="approveIdea(${i.id})" class="p-1.5 rounded-md hover:bg-green-50 text-green-600" title="Approve"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                    <button onclick="deleteIdea(${i.id})" class="p-1.5 rounded-md hover:bg-red-50 text-red-500" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

// ============================================================
// TABS
// ============================================================
function switchTab(tab) {
    STATE.currentTab = tab;
    ['Overview','Features','Logs','Ideas'].forEach(t => {
        document.getElementById('tab' + t).classList.remove('tab-active');
        document.getElementById('tab' + t).classList.add('border-transparent', 'text-slate-500');
        document.getElementById('content' + t).classList.add('hidden');
    });
    const tabEl = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
    tabEl.classList.add('tab-active');
    tabEl.classList.remove('border-transparent', 'text-slate-500');
    document.getElementById('content' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('hidden');

    if (tab === 'logs') renderLogs();
    if (tab === 'ideas') renderIdeas();
}

// ============================================================
// MODALS
// ============================================================
function openModal(type) {
    document.getElementById('modal' + capitalize(type)).classList.remove('hidden');
}
function closeModal(type) {
    document.getElementById('modal' + capitalize(type)).classList.add('hidden');
}

function openLogModal(featureId, featureName, currentStatus, currentProgress) {
    document.getElementById('logFeatureId').value = featureId;
    document.getElementById('logFeatureName').textContent = featureName;
    document.getElementById('logStatus').value = currentStatus;
    document.getElementById('logProgress').value = currentProgress;
    document.getElementById('logCatatan').value = '';
    openModal('log');
}

function populateModuleDropdowns() {
    const modules = STATE.modules;
    ['fModule', 'iModule'].forEach(id => {
        const sel = document.getElementById(id);
        const existing = sel.value;
        sel.innerHTML = id === 'iModule' ? '<option value="">-- Belum ditentukan --</option>' : '';
        modules.forEach(m => {
            sel.innerHTML += `<option value="${m.module_id || m.id}">${m.nama_module}</option>`;
        });
        if (existing) sel.value = existing;
    });
}

// ============================================================
// CRUD ACTIONS
// ============================================================
async function submitFeature(e) {
    e.preventDefault();
    const id = document.getElementById('featureId').value;
    const payload = {
        module_id: document.getElementById('fModule').value,
        kode_fitur: document.getElementById('fKode').value,
        nama_fitur: document.getElementById('fNama').value,
        slug: document.getElementById('fSlug').value,
        deskripsi: document.getElementById('fDeskripsi').value,
        status: document.getElementById('fStatus').value,
        progress_persen: parseInt(document.getElementById('fProgress').value) || 0,
        prioritas: document.getElementById('fPrioritas').value,
        assignee: document.getElementById('fAssignee').value,
        file_page: document.getElementById('fFilePage').value,
        file_controller: document.getElementById('fFileCtrl').value,
        file_route: document.getElementById('fFileRoute').value,
        catatan: document.getElementById('fCatatan').value,
        dikerjakan_oleh: document.getElementById('fAssignee').value || 'Nadhif'
    };

    let res;
    if (id) {
        payload.id = id;
        res = await api('update_feature', payload);
    } else {
        res = await api('create_feature', payload);
    }

    if (res.status === 200 || res.status === 201) {
        toast(id ? 'Fitur berhasil diupdate!' : 'Fitur berhasil ditambahkan!');
        closeModal('feature');
        document.getElementById('formFeature').reset();
        document.getElementById('featureId').value = '';
        loadAll();
    } else {
        toast(res.message || 'Gagal menyimpan', 'error');
    }
}

async function editFeature(id) {
    const res = await api('get_feature_detail', { id });
    if (res.status !== 200) return toast('Gagal load detail', 'error');

    const f = res.data.feature;
    document.getElementById('featureId').value = f.id;
    document.getElementById('fModule').value = f.module_id;
    document.getElementById('fKode').value = f.kode_fitur;
    document.getElementById('fNama').value = f.nama_fitur;
    document.getElementById('fSlug').value = f.slug || '';
    document.getElementById('fDeskripsi').value = f.deskripsi || '';
    document.getElementById('fStatus').value = f.status;
    document.getElementById('fProgress').value = f.progress_persen;
    document.getElementById('fPrioritas').value = f.prioritas;
    document.getElementById('fAssignee').value = f.assignee || '';
    document.getElementById('fFilePage').value = f.file_page || '';
    document.getElementById('fFileCtrl').value = f.file_controller || '';
    document.getElementById('fFileRoute').value = f.file_route || '';
    document.getElementById('fCatatan').value = '';
    document.getElementById('modalFeatureTitle').textContent = 'Edit Fitur: ' + f.nama_fitur;
    openModal('feature');
}

async function deleteFeature(id, nama) {
    if (!confirm(`Hapus fitur "${nama}"?`)) return;
    const res = await api('delete_feature', { id });
    if (res.status === 200) { toast('Fitur dihapus'); loadAll(); }
    else toast(res.message || 'Gagal', 'error');
}

async function submitLog(e) {
    e.preventDefault();
    const payload = {
        feature_id: document.getElementById('logFeatureId').value,
        status_sesudah: document.getElementById('logStatus').value,
        progress_sesudah: parseInt(document.getElementById('logProgress').value) || 0,
        catatan: document.getElementById('logCatatan').value,
        dikerjakan_oleh: document.getElementById('logOleh').value
    };

    const res = await api('create_log', payload);
    if (res.status === 201) {
        toast('Progress berhasil di-update!');
        closeModal('log');
        loadAll();
    } else {
        toast(res.message || 'Gagal', 'error');
    }
}

async function submitIdea(e) {
    e.preventDefault();
    const id = document.getElementById('ideaId').value;
    const payload = {
        judul: document.getElementById('iJudul').value,
        deskripsi: document.getElementById('iDeskripsi').value,
        module_id: document.getElementById('iModule').value || null,
        prioritas: document.getElementById('iPrioritas').value,
        diusulkan_oleh: document.getElementById('iOleh').value
    };

    let res;
    if (id) { payload.id = id; res = await api('update_idea', payload); }
    else { res = await api('create_idea', payload); }

    if (res.status === 200 || res.status === 201) {
        toast(id ? 'Ide diupdate!' : 'Ide berhasil ditambahkan!');
        closeModal('idea');
        document.getElementById('formIdea').reset();
        document.getElementById('ideaId').value = '';
        renderIdeas();
    } else {
        toast(res.message || 'Gagal', 'error');
    }
}

async function approveIdea(id) {
    const res = await api('update_idea', { id, status: 'approved' });
    if (res.status === 200) { toast('Ide di-approve!'); renderIdeas(); }
    else toast('Gagal', 'error');
}

async function deleteIdea(id) {
    if (!confirm('Hapus ide ini?')) return;
    const res = await api('delete_idea', { id });
    if (res.status === 200) { toast('Ide dihapus'); renderIdeas(); }
    else toast('Gagal', 'error');
}

// ============================================================
// HELPERS
// ============================================================
function capitalize(str) { return str.charAt(0).toUpperCase() + str.slice(1); }
function escape(str) { return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;'); }
function formatDate(d) { if (!d) return '-'; const dt = new Date(d); return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }); }
</script>
</body>
</html>
