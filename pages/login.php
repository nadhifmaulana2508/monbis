<?php
require_once __DIR__ . '/../components/login-showcase.php';
?>
<div class="mb-login-page">
  
  <?php mb_render_login_showcase(); ?>

  <div class="mb-login-form-pane">
    <div class="mb-login-card">
      <div class="mb-login-desktop-brand">
        <img src="./img/monbis-judul.png?v=4" alt="Monbis Monitoring Bisnis">
      </div>
      <div class="mb-login-mobile-brand">
        <img src="./img/monbis-icon.png?v=4" alt="Monbis">
      </div>
      <div class="mb-login-card__heading">
        <span class="mb-login-card__eyebrow">PORTAL INTERNAL MONBIS</span>
        <h1>Login Pegawai</h1>
        <p>Masukkan ID Pegawai dan password Anda.</p>
      </div>

      <div id="alreadyBox" class="hidden border-l-4 border-green-500 rounded-r bg-green-50 p-4 mb-6">
        <div class="text-sm text-gray-800 mb-3">
          Login sebagai <b id="alName" class="text-black"></b>.
        </div>
        <button id="btnGoHome" class="text-sm font-bold text-green-700 hover:underline mr-4">Ke Dashboard</button>
        <button id="btnSwitch" class="text-sm text-gray-600 hover:text-gray-900">Ganti Akun</button>
      </div>

      <form id="formLogin" class="mb-login-form">
        <div class="mb-login-field">
          <label for="employee_id">Employee ID</label>
          <input type="text" id="employee_id" class="mb-login-input" placeholder="Masukkan ID Pegawai" autocomplete="username" required>
        </div>

        <div class="mb-login-field">
          <div class="mb-login-field__label-row">
              <label for="password">Password</label>
              <!-- 🔥 TOMBOL LUPA PASSWORD 🔥 -->
              <button type="button" id="btnOpenForgot">Lupa Password?</button>
          </div>
          <div class="mb-login-input-wrap">
            <input type="password" id="password" class="mb-login-input mb-login-input--password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
            <button type="button" id="togglePwd" class="mb-login-password-toggle" aria-label="Tampilkan password">
               <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                 <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                 <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
               </svg>
            </button>
          </div>
        </div>

        <button type="submit" id="btnLogin" class="mb-login-submit">
            <svg id="spin" class="hidden animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span id="btnText">Verifikasi &amp; Masuk</span>
        </button>
        
        <div id="err" class="hidden flex mb-login-error" role="alert">
          <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
          <span id="errMsg">Info error disini</span>
        </div>
      </form>

      <div class="mb-login-security-note">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 19 6v5c0 4.5-2.9 8.3-7 9.8C7.9 19.3 5 15.5 5 11V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9.2 12 1.8 1.8 3.9-4.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Jaga kerahasiaan akun dan gunakan Monbis sebagai ruang belajar bersama.</span>
      </div>
    </div>
  </div>

  <!-- 🔥 MODAL RESET PASSWORD (3 STEP) 🔥 -->
  <div id="forgotModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">
      <!-- Header -->
      <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
        <h3 class="text-lg font-bold text-blue-900" id="modalTitle">Reset Password</h3>
        <button type="button" id="btnCloseModal" class="text-gray-400 hover:text-gray-700">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Alert Pesan (Error/Success di dalam Modal) -->
      <div id="modalAlert" class="hidden mx-6 mt-4 p-3 rounded text-sm font-medium"></div>

      <!-- STEP 1: Masukkan Email -->
      <div id="step1" class="p-6">
        <p class="text-sm text-gray-600 mb-4">Masukkan email yang terdaftar di sistem. Kami akan mengirimkan 6 digit kode OTP.</p>
        <form id="formStep1">
          <input type="email" id="forgotEmail" class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 py-3 px-4 focus:ring-blue-600 focus:border-blue-600 mb-4" placeholder="Alamat Email" required>
          <button type="submit" id="btnStep1" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg flex justify-center transition">
             <span id="textStep1">Kirim OTP</span>
          </button>
        </form>
      </div>

      <!-- STEP 2: Verifikasi OTP -->
      <div id="step2" class="hidden p-6">
        <p class="text-sm text-gray-600 mb-4">Cek kotak masuk email Anda. Masukkan 6 digit kode OTP yang baru saja kami kirimkan.</p>
        <form id="formStep2">
          <input type="text" id="forgotOtp" class="w-full rounded-lg border-gray-300 text-center tracking-widest text-2xl font-bold bg-gray-50 text-gray-900 py-3 px-4 focus:ring-blue-600 focus:border-blue-600 mb-4" placeholder="• • • • • •" maxlength="6" required>
          <button type="submit" id="btnStep2" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg flex justify-center transition">
             <span id="textStep2">Verifikasi OTP</span>
          </button>
        </form>
      </div>

      <!-- STEP 3: Buat Password Baru -->
      <div id="step3" class="hidden p-6">
        <p class="text-sm text-gray-600 mb-4">Kode OTP valid! Silakan buat password baru Anda sekarang.</p>
        <form id="formStep3">
          <input type="password" id="forgotNewPass" class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 py-3 px-4 focus:ring-blue-600 focus:border-blue-600 mb-4" placeholder="Password Baru" required>
          <button type="submit" id="btnStep3" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg flex justify-center transition">
             <span id="textStep3">Simpan Password Baru</span>
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
  // ... Config Base Path & Utils ...
  function getBasePath() {
    const baseTag = document.querySelector('base')?.getAttribute('href');
    if (baseTag) return new URL(baseTag, location.origin).pathname.replace(/\/+$/, '') || '';
    if (window.BASE_APP) return new URL(window.BASE_APP, location.origin).pathname.replace(/\/+$/, '') || '';
    if (location.pathname.startsWith('/report-dpk')) return '/report-dpk';
    return '';
  }
  const BASE_APP = window.BASE_APP || location.origin + getBasePath();
  const requestedNextPage = new URLSearchParams(window.location.search).get('next');
  const postLoginPage = requestedNextPage === 'tv_cabang' ? 'tv_cabang' : 'dashboard';
  
  // 1. DETEKSI ENVIRONMENT 
  const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
  const API_SSO_BASE = isLocal ? 'http://localhost/rest_api_sso' : 'https://apisso.bkkjateng.co.id';
  
  const API_LOGIN = `${API_SSO_BASE}/api/auth/login`;
  const API_WHOAMI = `${API_SSO_BASE}/api/auth/whoami`;
  
  // 🔥 API SSO RESET PASSWORD 🔥
  const API_FORGOT = `${API_SSO_BASE}/api/auth/forgot-password`;
  const API_VERIFY = `${API_SSO_BASE}/api/auth/verify-otp`;
  const API_RESET  = `${API_SSO_BASE}/api/auth/reset-password`;

  // STATE UNTUK TOKEN SEMENTARA
  let tempOtpToken = "";
  let tempResetToken = "";

  // 2. FUNGSI SET COOKIE SSO
  function setSSOCookie(name, value, days) {
      let expires = "";
      if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toUTCString();
      }
      const domainStr = isLocal ? "" : "domain=.bkkjateng.co.id;";
      document.cookie = name + "=" + (value || "")  + expires + "; path=/; " + domainStr + " SameSite=Lax"; 
  }

  const saveToken = (t) => {
      localStorage.setItem('dpk_token', t); 
      setSSOCookie('sso_token', t, 1);      
  };
  const saveUser = (u) => localStorage.setItem('dpk_user', JSON.stringify(u));
  
  // Toggle Password
  document.getElementById('togglePwd').addEventListener('click', () => {
    const inp = document.getElementById('password');
    inp.type = inp.type === 'password' ? 'text' : 'password';
  });

  // LOGIK LOGIN UTAMA
  document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('btnLogin');
    const spin = document.getElementById('spin');
    const btnText = document.getElementById('btnText');
    const errBox = document.getElementById('err');
    const errMsg = document.getElementById('errMsg');

    errBox.classList.add('hidden');
    btn.disabled = true;
    spin.classList.remove('hidden');
    btnText.textContent = 'Memeriksa...';

    const empId = document.getElementById('employee_id').value.trim();
    const pass  = document.getElementById('password').value;

    try {
        const res = await fetch(API_LOGIN, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_peg: empId, password: pass, app: "monbis" })
        });
        const json = await res.json().catch(() => ({}));
        const invalidLoginMessage = 'Data login belum sesuai. Silakan periksa kembali data Anda, lalu coba lagi.';

        if (res.status === 401 || json?.status === 401) {
            throw new Error(invalidLoginMessage);
        }
        if (!res.ok) throw new Error('Layanan login sedang tidak dapat digunakan. Silakan coba lagi beberapa saat.');

        if (json?.status !== 200 || !json?.data?.token) {
            throw new Error(invalidLoginMessage);
        }

        saveToken(json.data.token);
        
        try {
            const r2 = await fetch(API_WHOAMI, { 
                headers: { 'Authorization': `Bearer ${json.data.token}` }
            });
            if (r2.ok) {
                const j2 = await r2.json();
                if(j2?.data) {
                    let userData = j2.data;
                    if (userData.job_position === "Divisi Operasional" || userData.unit_kerja === "Divisi Operasional") {
                        userData.role = "dev";
                    } else {
                        userData.role = "user"; 
                    }
                    saveUser(userData);
                }
            }
        } catch (err) {
            console.error("Error mengambil data user (whoami):", err);
        }

        location.href = `${BASE_APP}/${postLoginPage}`;

    } catch (error) {
        errMsg.textContent = error.message.includes("Failed to fetch") 
            ? "Gagal terhubung ke server SSO. Pastikan API berjalan." 
            : error.message;
        errBox.classList.remove('hidden');
        btn.disabled = false;
        spin.classList.add('hidden');
        btnText.textContent = 'Verifikasi & Masuk';
    }
  });

  // ==========================================
  // 🔥 SCRIPT LOGIC MODAL RESET PASSWORD 🔥
  // ==========================================
  const modal = document.getElementById('forgotModal');
  const modalAlert = document.getElementById('modalAlert');
  
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');

  function showModalAlert(msg, isError = true) {
      modalAlert.classList.remove('hidden', 'bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800');
      modalAlert.classList.add(isError ? 'bg-red-100' : 'bg-green-100', isError ? 'text-red-800' : 'text-green-800');
      modalAlert.textContent = msg;
  }

  // Buka Modal
  document.getElementById('btnOpenForgot').addEventListener('click', () => {
      modal.classList.remove('hidden');
      step1.classList.remove('hidden');
      step2.classList.add('hidden');
      step3.classList.add('hidden');
      modalAlert.classList.add('hidden');
      document.getElementById('forgotEmail').value = "";
      document.getElementById('forgotOtp').value = "";
      document.getElementById('forgotNewPass').value = "";
  });

  // Tutup Modal
  document.getElementById('btnCloseModal').addEventListener('click', () => {
      modal.classList.add('hidden');
  });

  // ACTION STEP 1: KIRIM EMAIL -> DAPAT OTP TOKEN
  document.getElementById('formStep1').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnStep1');
      const txt = document.getElementById('textStep1');
      const email = document.getElementById('forgotEmail').value.trim();

      btn.disabled = true;
      txt.textContent = "Mengirim...";
      modalAlert.classList.add('hidden');

      try {
          const res = await fetch(API_FORGOT, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email: email })
          });
          const json = await res.json();

          if (res.status !== 200) throw new Error(json.message || 'Gagal mengirim OTP');

          tempOtpToken = json.data.otp_token; // Simpan token OTP di variabel JS

          showModalAlert("Kode OTP berhasil dikirim ke email Anda!", false);
          step1.classList.add('hidden');
          step2.classList.remove('hidden'); // Lanjut Step 2
      } catch (error) {
          showModalAlert(error.message);
      } finally {
          btn.disabled = false;
          txt.textContent = "Kirim OTP";
      }
  });

  // ACTION STEP 2: VERIFIKASI OTP -> DAPAT RESET TOKEN
  document.getElementById('formStep2').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnStep2');
      const txt = document.getElementById('textStep2');
      const otpCode = document.getElementById('forgotOtp').value.trim();

      btn.disabled = true;
      txt.textContent = "Mengecek...";
      modalAlert.classList.add('hidden');

      try {
          const res = await fetch(API_VERIFY, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ otp_token: tempOtpToken, otp_code: otpCode })
          });
          const json = await res.json();

          if (res.status !== 200) throw new Error(json.message || 'OTP tidak valid');

          tempResetToken = json.data.reset_token; // Simpan Token Reset

          showModalAlert("OTP Valid! Silakan buat password baru.", false);
          step2.classList.add('hidden');
          step3.classList.remove('hidden'); // Lanjut Step 3
      } catch (error) {
          showModalAlert(error.message);
      } finally {
          btn.disabled = false;
          txt.textContent = "Verifikasi OTP";
      }
  });

  // ACTION STEP 3: SUBMIT PASSWORD BARU
  document.getElementById('formStep3').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnStep3');
      const txt = document.getElementById('textStep3');
      const newPass = document.getElementById('forgotNewPass').value;

      btn.disabled = true;
      txt.textContent = "Menyimpan...";
      modalAlert.classList.add('hidden');

      try {
          const res = await fetch(API_RESET, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ reset_token: tempResetToken, new_password: newPass })
          });
          const json = await res.json();

          if (res.status !== 200) throw new Error(json.message || 'Gagal reset password');

          // Berhasil total!
          step3.innerHTML = `
              <div class="text-center py-6">
                 <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                 </svg>
                 <h4 class="text-xl font-bold text-gray-900 mb-2">Berhasil!</h4>
                 <p class="text-gray-600 mb-6">Password akun Anda telah berhasil diubah.</p>
                 <button type="button" onclick="document.getElementById('forgotModal').classList.add('hidden')" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg transition">Selesai & Login</button>
              </div>
          `;
      } catch (error) {
          showModalAlert(error.message);
          btn.disabled = false;
          txt.textContent = "Simpan Password Baru";
      }
  });
</script>
