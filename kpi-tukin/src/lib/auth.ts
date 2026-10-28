import { getToken } from "@/lib/utils"

export type MonbisUser = {
  id?: number | string
  employee_id?: string
  full_name?: string
  job_position?: string
  branch_name?: string
  kode?: string
  kode_kantor?: string
  kode_cabang?: string
  unit_kerja?: string
  level?: string
  group_jabatan?: string
  role?: string
  email?: string
  telp?: string
}

function readStoredUser() {
  if (typeof window === "undefined") return null
  const sources = [window.__USER, window.getUser?.()]
  for (const value of sources) {
    if (value && typeof value === "object") return value as MonbisUser
  }
  for (const key of ["dpk_user", "app_user", "user"]) {
    try {
      const value = JSON.parse(localStorage.getItem(key) || "null") as MonbisUser | null
      if (value && typeof value === "object") return value
    } catch {
      // Local storage can contain an incomplete value from an older session.
    }
  }
  return null
}

function ssoBaseUrl() {
  const isLocal = ["localhost", "127.0.0.1"].includes(window.location.hostname)
  return isLocal ? "http://localhost/rest_api_sso" : "https://apisso.bkkjateng.co.id"
}

export async function loginWithMonbis(employeeId: string, password: string) {
  const response = await fetch(`${ssoBaseUrl()}/api/auth/login`, {
    method: "POST",
    headers: { Accept: "application/json", "Content-Type": "application/json" },
    body: JSON.stringify({ id_peg: employeeId.trim(), password, app: "monbis" }),
    cache: "no-store",
  })
  const payload = await response.json().catch(() => ({})) as {
    status?: number | string
    message?: string
    data?: { token?: string }
  }
  const token = payload.data?.token
  if (!response.ok || Number(payload.status) !== 200 || !token) {
    throw new Error(payload.message || "ID pegawai atau password belum sesuai.")
  }

  const whoami = await fetch(`${ssoBaseUrl()}/api/auth/whoami`, {
    headers: { Accept: "application/json", Authorization: `Bearer ${token}` },
    cache: "no-store",
  })
  const identity = await whoami.json().catch(() => ({})) as { status?: number | string; data?: MonbisUser }
  if (!whoami.ok || Number(identity.status) !== 200 || !identity.data) {
    throw new Error("Login berhasil, tetapi data pegawai belum dapat diverifikasi.")
  }

  const secure = window.location.protocol === "https:" ? "; Secure" : ""
  const localHost = ["localhost", "127.0.0.1", "::1"].includes(window.location.hostname)
  const domain = localHost ? "" : "; domain=.bkkjateng.co.id"
  document.cookie = `sso_token=${encodeURIComponent(token)}; max-age=86400; path=/${domain}; SameSite=Lax${secure}`
  try {
    localStorage.setItem("dpk_token", token)
    localStorage.setItem("dpk_user", JSON.stringify(identity.data))
    localStorage.setItem("dpk_user_verified_at", String(Date.now()))
  } catch { /* Cookie remains the primary session source. */ }
  window.__USER = identity.data
  return identity.data
}

export async function loadCurrentUser(signal?: AbortSignal) {
  const stored = readStoredUser()
  const token = getToken()
  if (!token) return stored

  try {
    const response = await fetch(`${ssoBaseUrl()}/api/auth/whoami`, {
      headers: { Accept: "application/json", Authorization: `Bearer ${token}` },
      cache: "no-store",
      signal,
    })
    const json = await response.json().catch(() => ({})) as { data?: MonbisUser }
    if (!response.ok || !json.data) return stored
    try { localStorage.setItem("dpk_user", JSON.stringify(json.data)) } catch { /* storage is optional */ }
    return { ...stored, ...json.data }
  } catch (error) {
    if (error instanceof DOMException && error.name === "AbortError") throw error
    return stored
  }
}

export async function validateKpiSession(signal?: AbortSignal) {
  const token = getToken()
  if (!token) return null

  try {
    const response = await fetch(`${ssoBaseUrl()}/api/auth/whoami`, {
      headers: { Accept: "application/json", Authorization: /^Bearer\s/i.test(token) ? token : `Bearer ${token}` },
      cache: "no-store",
      signal,
    })
    const payload = await response.json().catch(() => ({})) as { status?: number | string; data?: MonbisUser }
    if (!response.ok || Number(payload.status) !== 200 || !payload.data || typeof payload.data !== "object") return null
    try { localStorage.setItem("dpk_user", JSON.stringify(payload.data)) } catch { /* Storage is optional. */ }
    return payload.data
  } catch (error) {
    if (error instanceof DOMException && error.name === "AbortError") throw error
    return null
  }
}

export function userOfficeCode(user: MonbisUser | null) {
  const raw = user?.kode_kantor || user?.kode || user?.kode_cabang || "000"
  const digits = String(raw).replace(/\D/g, "")
  return digits ? digits.padStart(3, "0").slice(-3) : "000"
}

export function userInitials(user: MonbisUser | null) {
  const name = String(user?.full_name || "Pengguna").trim()
  return name.split(/\s+/).slice(0, 2).map((part) => part[0]).join("").toUpperCase() || "P"
}

declare global {
  interface Window {
    __USER?: MonbisUser
    getUser?: () => MonbisUser | null
  }
}
