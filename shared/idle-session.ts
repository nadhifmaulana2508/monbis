const IDLE_TIMEOUT_MS = 7_200_000
const ACTIVITY_KEY = "monbis:last-activity"
const LOGOUT_KEY = "monbis:idle-logout"

function readActivity() {
  try { return Number(localStorage.getItem(ACTIVITY_KEY)) }
  catch { return 0 }
}

function writeValue(key: string, value: string) {
  try { localStorage.setItem(key, value) }
  catch { /* In-memory timeout still works when storage is blocked. */ }
}

export function installIdleLogout(active: boolean, onIdle: () => void) {
  let expired = false
  const now = Date.now()
  let lastActivity = readActivity()
  if (!Number.isFinite(lastActivity) || lastActivity <= 0) {
    lastActivity = now
    writeValue(ACTIVITY_KEY, String(now))
  }
  let lastPersistedActivity = lastActivity

  const touch = () => {
    if (expired) return
    lastActivity = Date.now()
    if (lastActivity - lastPersistedActivity >= 1_000) {
      lastPersistedActivity = lastActivity
      writeValue(ACTIVITY_KEY, String(lastActivity))
    }
  }

  const expire = (broadcast: boolean) => {
    if (expired) return
    expired = true
    if (broadcast) writeValue(LOGOUT_KEY, String(Date.now()))
    onIdle()
  }

  const checkIdle = () => {
    const sharedActivity = readActivity()
    if (Number.isFinite(sharedActivity) && sharedActivity > lastActivity) lastActivity = sharedActivity
    if (active && Date.now() - lastActivity >= IDLE_TIMEOUT_MS) expire(true)
  }

  const onStorage = (event: StorageEvent) => {
    if (event.key === ACTIVITY_KEY && event.newValue) {
      const sharedActivity = Number(event.newValue)
      if (Number.isFinite(sharedActivity)) lastActivity = sharedActivity
    }
    if (event.key === LOGOUT_KEY && event.newValue && active) expire(false)
  }

  const activityEvents: Array<keyof WindowEventMap> = ["pointerdown", "keydown", "scroll", "touchstart", "click", "mousemove"]
  activityEvents.forEach((eventName) => window.addEventListener(eventName, touch, { passive: true }))
  window.addEventListener("storage", onStorage)
  const timer = window.setInterval(checkIdle, 1_000)

  if (active) checkIdle()

  return () => {
    activityEvents.forEach((eventName) => window.removeEventListener(eventName, touch))
    window.removeEventListener("storage", onStorage)
    window.clearInterval(timer)
  }
}
