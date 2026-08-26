<?php /* pages/login.php */ ?>
<div class="min-h-screen flex bg-white text-gray-900 font-sans overflow-hidden relative">
  
  <style>
    /* Keyframes untuk pergerakan meteor */
    @keyframes meteor {
      0% { transform: rotate(215deg) translateX(0); opacity: 1; }
      70% { opacity: 1; }
      100% { transform: rotate(215deg) translateX(-500px); opacity: 0; }
    }

    /* Style dasar meteor */
    .meteor-effect {
      position: absolute;
      top: 50%;
      left: 50%;
      height: 2px;
      background: linear-gradient(-45deg, #5f91ff, rgba(0, 0, 255, 0));
      border-radius: 999px;
      filter: drop-shadow(0 0 6px #69a0ff);
      animation: meteor 3s linear infinite;
      opacity: 0;
      z-index: 10;
    }
    
    .meteor-effect::before {
      content: '';
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 300px;
      height: 1px;
      background: linear-gradient(90deg, #fff, transparent);
    }
  </style>

  <div class="hidden lg:flex lg:w-1/2 relative bg-blue-900 text-white flex-col justify-center items-center overflow-hidden">
    <div class="absolute inset-0 z-0" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80'); background-size: cover; background-position: center;"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-gray-900/80 via-blue-900/80 to-black/80 z-0"></div>

    <span class="meteor-effect w-[100px]" style="top: -10%; left: 20%; animation-duration: 4s; animation-delay: 0s;"></span>
    <span class="meteor-effect w-[150px]" style="top: 10%; left: 80%; animation-duration: 3s; animation-delay: 1.5s;"></span>
    <span class="meteor-effect w-[120px]" style="top: -20%; left: 50%; animation-duration: 5s; animation-delay: 2s;"></span>
    <span class="meteor-effect w-[180px]" style="top: 30%; left: 110%; animation-duration: 3.5s; animation-delay: 0.5s;"></span>
    <span class="meteor-effect w-[140px]" style="top: 5%; left: 60%; animation-duration: 6s; animation-delay: 3s;"></span>
    <span class="meteor-effect w-[200px]" style="top: -15%; left: 10%; animation-duration: 4.5s; animation-delay: 4s;"></span>
    <span class="meteor-effect w-[160px]" style="top: 15%; left: 90%; animation-duration: 5.5s; animation-delay: 1s;"></span>
    
    <div class="relative z-20 text-center px-10">
      <div class="mb-6 inline-flex p-4 bg-white/10 rounded-full backdrop-blur-sm border border-white/20 shadow-[0_0_15px_rgba(255,255,255,0.3)]">
         <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
         </svg>
      </div>
      <h2 class="text-4xl font-bold mb-4 tracking-tight text-white drop-shadow-lg">MONBIS</h2>
      <p class="text-blue-100 text-lg drop-shadow-md">MONITORING BISNIS BKK JATENG (PERSERODA)</p>
      <p class="text-blue-200 text-sm mt-2 opacity-80">Secure Portal</p>
    </div>
  </div>

  <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 text-gray-900 relative">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
      <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Login Pegawai</h1>
        <p class="text-gray-500 text-sm">Masukkan ID Pegawai dan Password Anda.</p>
      </div>

      <div id="alreadyBox" class="hidden border-l-4 border-green-500 rounded-r bg-green-50 p-4 mb-6">
        <div class="text-sm text-gray-800 mb-3">
          Login sebagai <b id="alName" class="text-black"></b>.
        </div>
        <button id="btnGoHome" class="text-sm font-bold text-green-700 hover:underline mr-4">Ke Dashboard</button>
        <button id="btnSwitch" class="text-sm text-gray-600 hover:text-gray-900">Ganti Akun</button>
      </div>

      <form id="formLogin" class="space-y-5">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Employee ID</label>
          <input type="text" id="employee_id" 
                 class="w-full rounded-lg border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4"
                 placeholder="ID Pegawai" required>
        </div>

        <div>
          <div class="flex justify-between items-center mb-1">
              <label class="block text-sm font-bold text-gray-700">Password</label>
              <!-- 🔥 TOMBOL LUPA PASSWORD 🔥 -->
              <button type="button" id="btnOpenForgot" class="text-xs font-bold text-blue-600 hover:text-blue-800 focus:outline-none">Lupa Password?</button>
          </div>
          <div class="relative">
            <input type="password" id="password" 
                   class="w-full rounded-lg border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-600 focus:ring-blue-600 py-3 px-4 pr-10"
                   placeholder="Kata sandi" required>
            <button type="button" id="togglePwd" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                 <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                 <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
               </svg>
            </button>
          </div>
        </div>

        <button type="submit" id="btnLogin" 
            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-blue-700 hover:bg-blue-800 transition-all transform hover:-translate-y-0.5">
            <svg id="spin" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span id="btnText">Verifikasi & Masuk</span>
        </button>
        
        <div id="err" class="hidden flex items-center p-4 text-sm text-red-800 border border-red-200 rounded-lg bg-red-50" role="alert">
          <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
          <span id="errMsg">Info error disini</span>
        </div>
      </form>
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
        if (!res.ok) throw new Error(`HTTP Error: ${res.status} - Gagal koneksi ke server.`);
        const json = await res.json();

        if (json?.status !== 200 || !json?.data?.token) {
            throw new Error(json?.message || 'ID Pegawai atau Password salah.');
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
