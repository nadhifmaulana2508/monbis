(() => {
  const timeoutMs = 7_200_000;
  const activityKey = "monbis:last-activity";
  const logoutKey = "monbis:idle-logout";
  const baseUrl = String(window.MONBIS_CONFIG?.baseUrl || "").replace(/\/$/, "");
  const readActivity = () => {
    try { return Number(localStorage.getItem(activityKey)); }
    catch { return 0; }
  };
  const writeValue = (key, value) => {
    try { localStorage.setItem(key, value); }
    catch { /* In-memory timeout still works when storage is blocked. */ }
  };
  let lastActivity = readActivity();
  let expired = false;
  let lastPersistedActivity = lastActivity;
  const authenticated = Boolean(window.MONBIS_CONFIG?.authenticated);

  if (!Number.isFinite(lastActivity) || lastActivity <= 0) {
    lastActivity = Date.now();
    writeValue(activityKey, String(lastActivity));
    lastPersistedActivity = lastActivity;
  }

  const finish = async (broadcast) => {
    if (expired) return;
    expired = true;
    if (broadcast) writeValue(logoutKey, String(Date.now()));
    try {
      localStorage.removeItem("dpk_token");
      localStorage.removeItem("dpk_user");
      localStorage.removeItem("dpk_user_verified_at");
    } catch {
      // Cookie logout and login redirect still proceed when storage is blocked.
    }
    try {
      await fetch(`${baseUrl}/api/auth/logout`, {
        method: "POST",
        credentials: "include",
        cache: "no-store",
        keepalive: true,
      });
    } catch {
      // Navigasi login tetap dilakukan saat endpoint logout tidak terjangkau.
    }
    window.location.replace(`${baseUrl}/login`);
  };

  const touch = () => {
    if (expired) return;
    lastActivity = Date.now();
    if (lastActivity - lastPersistedActivity >= 1_000) {
      lastPersistedActivity = lastActivity;
      writeValue(activityKey, String(lastActivity));
    }
  };

  const checkIdle = () => {
    const sharedActivity = readActivity();
    if (Number.isFinite(sharedActivity) && sharedActivity > lastActivity) lastActivity = sharedActivity;
    if (authenticated && Date.now() - lastActivity >= timeoutMs) void finish(true);
  };

  const activityEvents = ["pointerdown", "keydown", "scroll", "touchstart", "click", "mousemove"];
  activityEvents.forEach((eventName) => window.addEventListener(eventName, touch, { passive: true }));
  window.addEventListener("storage", (event) => {
    if (event.key === activityKey && event.newValue) {
      const sharedActivity = Number(event.newValue);
      if (Number.isFinite(sharedActivity)) lastActivity = sharedActivity;
    }
    if (event.key === logoutKey && event.newValue && authenticated) void finish(false);
  });
  window.setInterval(checkIdle, 1_000);
  checkIdle();
})();
