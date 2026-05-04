<?php /* pages/profile.php */ ?>
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
  <div class="max-w-6xl mx-auto">
    
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-extrabold text-gray-900">Profil Saya</h1>
      <p class="text-gray-500 mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>
    </div>

    <!-- Alert Global -->
    <div id="alertBox" class="hidden mb-6 p-4 rounded-lg flex items-center shadow-sm transition-all" role="alert">
      <svg id="alertIcon" class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
      <span id="alertMsg" class="font-medium text-sm"></span>
      <button type="button" onclick="document.getElementById('alertBox').classList.add('hidden')" class="ml-auto text-gray-500 hover:text-gray-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
      
      <!-- ======================================= -->
      <!-- KOLOM KIRI: KARTU PROFIL SINGKAT -->
      <!-- ======================================= -->
      <div class="md:col-span-4 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center h-full">
          <div class="w-28 h-28 mx-auto bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-5xl font-bold mb-5 shadow-inner">
            <span id="avatarInitial">U</span>
          </div>
          <h2 id="dispName" class="text-xl font-bold text-gray-900 leading-tight">-</h2>
          <p class="text-sm font-semibold text-blue-600 mt-2 bg-blue-50 inline-block px-3 py-1 rounded-full border border-blue-100">
            ID: <span id="dispId">-</span>
          </p>
          
          <hr class="my-5 border-gray-100">
          
          <div class="text-left space-y-3">
            <div>
              <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Jabatan Utama</p>
              <p id="dispJob" class="text-sm text-gray-800 font-medium">-</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kantor</p>
              <p id="dispBranch" class="text-sm text-gray-800 font-medium">-</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================= -->
      <!-- KOLOM KANAN: TAB KONTEN -->
      <!-- ======================================= -->
      <div class="md:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
        
        <!-- NAVIGASI TAB -->
        <div class="bg-gray-50/50 border-b border-gray-100 px-2 pt-2 sm:px-6 sm:pt-4 flex space-x-1 sm:space-x-4 overflow-x-auto">
          <button onclick="switchTab('detail')" id="btnTab-detail" class="tab-btn active px-4 py-3 text-sm font-bold border-b-2 border-blue-600 text-blue-600 whitespace-nowrap hover:text-blue-700 transition-colors">
            Detail Profil
          </button>
          <button onclick="switchTab('kontak')" id="btnTab-kontak" class="tab-btn px-4 py-3 text-sm font-bold border-b-2 border-transparent text-gray-500 whitespace-nowrap hover:text-gray-700 hover:border-gray-300 transition-colors">
            Edit Kontak
          </button>
          <button onclick="switchTab('password')" id="btnTab-password" class="tab-btn px-4 py-3 text-sm font-bold border-b-2 border-transparent text-gray-500 whitespace-nowrap hover:text-gray-700 hover:border-gray-300 transition-colors">
            Ganti Password
          </button>
        </div>

        <!-- ISI TAB 1: DETAIL PROFIL LENGKAP -->
        <div id="tab-detail" class="tab-content p-6 flex-grow">
          
          <!-- 🔥 HEADER DENGAN TOMBOL WHATSAPP 🔥 -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
              <h3 class="text-lg font-bold text-gray-900">Informasi Kepegawaian</h3>
              <button onclick="laporDataWA()" class="inline-flex items-center text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg border border-orange-200 transition-colors shadow-sm focus:outline-none">
                  <!-- Icon Warning -->
                  <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  Data tidak sesuai?
              </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
              <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Kode Cabang</p>
              <p id="detKode" class="text-base text-gray-900 font-semibold">-</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
              <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Unit Kerja</p>
              <p id="detUnit" class="text-base text-gray-900 font-semibold">-</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
              <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Level Jabatan</p>
              <p id="detLevel" class="text-base text-gray-900 font-semibold">-</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
              <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Group Jabatan</p>
              <p id="detGroup" class="text-base text-gray-900 font-semibold">-</p>
            </div>
          </div>
        </div>

        <!-- ISI TAB 2: EDIT KONTAK -->
        <div id="tab-kontak" class="tab-content hidden p-6 flex-grow">
          <h3 class="text-lg font-bold text-gray-900 mb-1">Perbarui Kontak</h3>
          <p class="text-sm text-gray-500 mb-6">Pastikan email dan nomor telepon selalu aktif untuk menerima kode OTP.</p>
          <form id="formProfile" class="space-y-6 max-w-lg">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
              <input type="email" id="profEmail" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4" placeholder="contoh@gmail.com">
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Telepon / WA</label>
              <input type="text" id="profTelp" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4" placeholder="0812xxxxxx">
            </div>
            <div class="pt-2">
              <button type="submit" id="btnSaveProfile" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                <span id="txtSaveProfile">Simpan Perubahan Kontak</span>
              </button>
            </div>
          </form>
        </div>

        <!-- ISI TAB 3: GANTI PASSWORD -->
        <div id="tab-password" class="tab-content hidden p-6 flex-grow">
          <h3 class="text-lg font-bold text-gray-900 mb-1">Ganti Password</h3>
          <p class="text-sm text-gray-500 mb-6">Gunakan kombinasi huruf dan angka agar akun Anda lebih aman.</p>
          <form id="formPassword" class="space-y-6 max-w-lg">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Password Lama</label>
              <input type="password" id="oldPass" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4" required>
            </div>
            <hr class="border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
                <input type="password" id="newPass" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4" required>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                <input type="password" id="confPass" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4" required>
              </div>
            </div>
            <div class="pt-2">
              <button type="submit" id="btnSavePass" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 transition-colors">
                <span id="txtSavePass">Ubah Password Sekarang</span>
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  // ==========================================
  // 🔥 FUNGSI LAPOR WA (OTOMATIS NGISI PESAN) 🔥
  // ==========================================
  function laporDataWA() {
      // 1. Nomor WA Tujuan (Ubah awalan 0 menjadi 62)
      const targetPhone = "6288228659668";
      
      // 2. Ambil data nama dan ID dari JSON
      const empName = user.full_name || "Tanpa Nama";
      const empId = user.employee_id || "Tidak Diketahui";

      // 3. Rangkai pesan otomatis
      const textMessage = `Halo Admin SIMPEG BKK, \n\nMohon bantuan untuk pembaruan data karena terdapat ketidaksesuaian data pada sistem MONBIS. \n\nBerikut data saya:\n*Nama:* ${empName}\n*ID Pegawai:* ${empId}\n\nTerima kasih.`;

      // 4. Encode URL agar spasi dan enter bisa dibaca browser
      const encodedMessage = encodeURIComponent(textMessage);

      // 5. Buka tab baru langsung ke aplikasi WA
      const waUrl = `https://wa.me/${targetPhone}?text=${encodedMessage}`;
      window.open(waUrl, '_blank');
  }

  // ==========================================
  // LOGIKA TAB MENU
  // ==========================================
  function switchTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.tab-btn').forEach(btn => {
          btn.classList.remove('border-blue-600', 'text-blue-600', 'active');
          btn.classList.add('border-transparent', 'text-gray-500');
      });
      document.getElementById('tab-' + tabId).classList.remove('hidden');
      const activeBtn = document.getElementById('btnTab-' + tabId);
      activeBtn.classList.remove('border-transparent', 'text-gray-500');
      activeBtn.classList.add('border-blue-600', 'text-blue-600', 'active');
  }

  // ==========================================
  // CONFIG & UTILS
  // ==========================================
  const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
  const API_SSO_BASE = isLocal ? 'http://localhost/rest_api_sso' : 'https://apisso.bkkjateng.co.id';
  
  const API_UPDATE_PROFILE = `${API_SSO_BASE}/api/auth/update-profile`;
  const API_CHANGE_PASS    = `${API_SSO_BASE}/api/auth/change-password`;

  const token = localStorage.getItem('dpk_token');
  let user = JSON.parse(localStorage.getItem('dpk_user')) || {};

  if (!token) {
      window.location.href = '/login'; 
  }

  // ==========================================
  // RENDER SEMUA DATA DARI JSON
  // ==========================================
  function renderUserData() {
      // Data Kartu Kiri (Singkat)
      document.getElementById('dispName').textContent = user.full_name || 'Tanpa Nama';
      document.getElementById('dispId').textContent = user.employee_id || '-';
      document.getElementById('dispJob').textContent = user.job_position || '-';
      document.getElementById('dispBranch').textContent = user.branch_name || '-';
      
      if (user.full_name) {
          document.getElementById('avatarInitial').textContent = user.full_name.charAt(0).toUpperCase();
      }

      // Data Tab 1 (Detail Lengkap)
      document.getElementById('detKode').textContent = user.kode || '-';
      document.getElementById('detUnit').textContent = user.unit_kerja || '-';
      document.getElementById('detLevel').textContent = user.level || '-';
      document.getElementById('detGroup').textContent = user.group_jabatan || '-';

      // Data Tab 2 (Form Kontak)
      document.getElementById('profEmail').value = user.email || '';
      document.getElementById('profTelp').value = user.telp || '';
  }
  
  renderUserData();

  // ==========================================
  // FUNGSI ALERT GLOBAL
  // ==========================================
  function showAlert(message, type = 'success') {
      const box = document.getElementById('alertBox');
      const msg = document.getElementById('alertMsg');
      const icon = document.getElementById('alertIcon');

      box.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
      
      if (type === 'success') {
          box.classList.add('bg-green-100', 'text-green-800');
          icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
      } else {
          box.classList.add('bg-red-100', 'text-red-800');
          icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
      }
      
      msg.textContent = message;
      setTimeout(() => box.classList.add('hidden'), 5000);
  }

  // ==========================================
  // ACTION: UPDATE EMAIL & TELP
  // ==========================================
  document.getElementById('formProfile').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnSaveProfile');
      const txt = document.getElementById('txtSaveProfile');
      
      const emailVal = document.getElementById('profEmail').value.trim();
      const telpVal = document.getElementById('profTelp').value.trim();

      btn.disabled = true;
      txt.textContent = 'Menyimpan...';

      try {
          const res = await fetch(API_UPDATE_PROFILE, {
              method: 'POST',
              headers: { 
                  'Content-Type': 'application/json',
                  'Authorization': `Bearer ${token}` 
              },
              body: JSON.stringify({ email: emailVal, telp: telpVal })
          });
          
          const json = await res.json();
          if (!res.ok) throw new Error(json.message || 'Gagal update profil');

          user.email = emailVal;
          user.telp = telpVal;
          localStorage.setItem('dpk_user', JSON.stringify(user));

          showAlert('Data kontak berhasil diperbarui!', 'success');
      } catch (error) {
          showAlert(error.message, 'error');
      } finally {
          btn.disabled = false;
          txt.textContent = 'Simpan Perubahan Kontak';
      }
  });

  // ==========================================
  // ACTION: GANTI PASSWORD
  // ==========================================
  document.getElementById('formPassword').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnSavePass');
      const txt = document.getElementById('txtSavePass');
      
      const oldPass = document.getElementById('oldPass').value;
      const newPass = document.getElementById('newPass').value;
      const confPass = document.getElementById('confPass').value;

      if (newPass !== confPass) {
          showAlert('Password Baru dan Konfirmasi Password tidak cocok!', 'error');
          return;
      }
      if (newPass.length < 6) {
          showAlert('Password baru minimal 6 karakter!', 'error');
          return;
      }

      btn.disabled = true;
      txt.textContent = 'Memproses...';

      try {
          const res = await fetch(API_CHANGE_PASS, {
              method: 'POST',
              headers: { 
                  'Content-Type': 'application/json',
                  'Authorization': `Bearer ${token}` 
              },
              body: JSON.stringify({ old_password: oldPass, new_password: newPass })
          });
          
          const json = await res.json();
          if (!res.ok) throw new Error(json.message || 'Gagal mengubah password');

          showAlert('Password berhasil diubah! Silakan gunakan password baru untuk login berikutnya.', 'success');
          document.getElementById('formPassword').reset();
      } catch (error) {
          showAlert(error.message, 'error');
      } finally {
          btn.disabled = false;
          txt.textContent = 'Ubah Password Sekarang';
      }
  });
</script>