<div id="tvLoginModal" class="fixed inset-0 bg-gray-950 bg-opacity-95 z-50 flex flex-col justify-center items-center hidden">
    <div class="bg-gray-800 p-8 rounded-2xl shadow-2xl max-w-sm w-full text-center border border-gray-700">
        <h2 class="text-2xl font-black text-blue-400 mb-2">📺 TV Kiosk Mode</h2>
        <p class="text-sm text-gray-400 mb-6">Masukkan Kode Akses Direksi</p>
        <input type="password" id="tvPinInput" class="w-full text-center text-3xl font-bold tracking-[0.5em] p-3 border-2 border-gray-600 bg-gray-900 text-white rounded-xl focus:border-blue-500 outline-none mb-4" maxlength="6">
        <button onclick="verifyTvPin()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all">Akses Dashboard</button>
        <p id="tvLoginError" class="text-red-500 text-xs font-bold mt-3 hidden">Kode PIN Salah!</p>
    </div>
</div>