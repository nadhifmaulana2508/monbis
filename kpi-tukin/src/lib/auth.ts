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

export function userOfficeCode(user: MonbisUser | null) {
  const raw = user?.kode_kantor || user?.kode || user?.kode_cabang || "000"
  const digits = String(raw).replace(/\D/g, "")
  return digits ? digits.padStart(3, "0").slice(-3) : "000"
}

export function userInitials(user: MonbisUser | null) {
  const name = String(user?.full_name || "MONBIS").trim()
  return name.split(/\s+/).slice(0, 2).map((part) => part[0]).join("").toUpperCase() || "M"
}

declare global {
  interface Window {
    __USER?: MonbisUser
    getUser?: () => MonbisUser | null
  }
}
