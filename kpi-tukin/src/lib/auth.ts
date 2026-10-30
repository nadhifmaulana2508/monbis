import { getToken } from "@/lib/utils"
import { apiUrl } from "@/lib/api-base"

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

type AuthResponse<T> = {
  status?: number | string
  message?: string
  data?: T
}

async function requestAuth<T>(path: string, init: RequestInit = {}) {
  let response: Response
  try {
    response = await fetch(apiUrl(`auth/${path}`), {
      ...init,
      credentials: "include",
      headers: {
        Accept: "application/json",
        ...(init.body ? { "Content-Type": "application/json" } : {}),
        ...init.headers,
      },
      cache: "no-store",
    })
  } catch {
    throw new Error("Gagal terhubung ke layanan autentikasi MONBIS.")
  }

  const payload = await response.json().catch(() => ({})) as AuthResponse<unknown>
  if (!response.ok || Number(payload.status) !== 200) {
    throw new Error(payload.message || "Permintaan autentikasi gagal.")
  }
  return payload as AuthResponse<T>
}

function saveUser(user: MonbisUser) {
  try {
    localStorage.removeItem("dpk_token")
    localStorage.setItem("dpk_user", JSON.stringify(user))
    localStorage.setItem("dpk_user_verified_at", String(Date.now()))
  } catch { /* Session cookie is managed by the server. */ }
  document.cookie = "sso_token=; max-age=0; path=/; SameSite=Lax"
  const hostname = window.location.hostname.toLowerCase()
  if (hostname === "bkkjateng.co.id" || hostname.endsWith(".bkkjateng.co.id")) {
    document.cookie = "sso_token=; max-age=0; path=/; domain=.bkkjateng.co.id; SameSite=Lax"
  }
  window.AUTH_TOKEN = ""
  window.__USER = user
}

export async function loginWithMonbis(employeeId: string, password: string) {
  const payload = await requestAuth<MonbisUser>("login", {
    method: "POST",
    body: JSON.stringify({ id_peg: employeeId.trim(), password }),
  })
  if (!payload.data || typeof payload.data !== "object") {
    throw new Error("Data pegawai dari SSO tidak dapat diverifikasi.")
  }

  saveUser(payload.data)
  return payload.data
}

export async function loadCurrentUser(signal?: AbortSignal) {
  try {
    const token = getToken()
    const payload = await requestAuth<MonbisUser>("whoami", {
      headers: token ? { Authorization: /^Bearer\s/i.test(token) ? token : `Bearer ${token}` } : {},
      signal,
    })
    if (!payload.data || typeof payload.data !== "object") return null
    saveUser(payload.data)
    return payload.data
  } catch (error) {
    if (error instanceof DOMException && error.name === "AbortError") throw error
    return null
  }
}

export async function validateKpiSession(signal?: AbortSignal) {
  try {
    const token = getToken()
    const payload = await requestAuth<MonbisUser>("whoami", {
      headers: token ? { Authorization: /^Bearer\s/i.test(token) ? token : `Bearer ${token}` } : {},
      signal,
    })
    if (!payload.data || typeof payload.data !== "object") return null
    saveUser(payload.data)
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
