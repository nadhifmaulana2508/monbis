import { getToken } from "@/lib/utils"

export type RbbUser = {
  id?: number | string
  employee_id?: string
  full_name?: string
  job_position?: string
  branch_name?: string
  kode?: string
  kode_kantor?: string
  unit_kerja?: string
}

type SsoEnvelope<T> = { status?: number | string; message?: string; data?: T }

function ssoBaseUrl() {
  const hostname = window.location.hostname
  return hostname === "localhost" || hostname === "127.0.0.1"
    ? "http://localhost/rest_api_sso"
    : "https://apisso.bkkjateng.co.id"
}

function storeSession(token: string, user: RbbUser) {
  const localHost = ["localhost", "127.0.0.1", "::1"].includes(window.location.hostname)
  const domain = localHost ? "" : "; domain=.bkkjateng.co.id"
  const secure = window.location.protocol === "https:" ? "; Secure" : ""
  document.cookie = `sso_token=${encodeURIComponent(token)}; max-age=86400; path=/${domain}; SameSite=Lax${secure}`
  try {
    localStorage.setItem("dpk_token", token)
    localStorage.setItem("dpk_user", JSON.stringify(user))
  } catch {
    // Cookie remains available when local storage is disabled.
  }
}

async function requestSso<T>(path: string, init?: RequestInit) {
  let response: Response
  try {
    response = await fetch(`${ssoBaseUrl()}/api/auth/${path}`, { cache: "no-store", ...init })
  } catch {
    throw new Error("Gagal terhubung ke SSO MONBIS. Periksa koneksi lalu coba lagi.")
  }
  const payload = await response.json().catch(() => ({})) as SsoEnvelope<T>
  if (!response.ok || Number(payload.status) !== 200 || !payload.data) {
    throw new Error(payload.message || "Sesi MONBIS tidak valid. Silakan masuk kembali.")
  }
  return payload.data
}

export async function loginWithMonbis(employeeId: string, password: string) {
  const result = await requestSso<{ token?: string }>("login", {
    method: "POST",
    headers: { Accept: "application/json", "Content-Type": "application/json" },
    body: JSON.stringify({ id_peg: employeeId.trim(), password, app: "monbis" }),
  })
  if (!result.token) throw new Error("Token login MONBIS tidak ditemukan.")
  const user = await requestSso<RbbUser>("whoami", {
    headers: { Accept: "application/json", Authorization: `Bearer ${result.token}` },
  })
  if (!user.employee_id) throw new Error("Data pegawai belum dapat diverifikasi.")
  storeSession(result.token, user)
  return user
}

export async function validateRbbSession(signal?: AbortSignal) {
  const token = getToken()
  if (!token) return null
  try {
    const user = await requestSso<RbbUser>("whoami", {
      headers: { Accept: "application/json", Authorization: /^Bearer\s/i.test(token) ? token : `Bearer ${token}` },
      signal,
    })
    if (!user.employee_id) return null
    try { localStorage.setItem("dpk_user", JSON.stringify(user)) } catch { /* Storage is optional. */ }
    return user
  } catch (error) {
    if (error instanceof DOMException && error.name === "AbortError") throw error
    return null
  }
}

export function clearRbbSession() {
  try { localStorage.removeItem("dpk_token") } catch { /* Cookie can still be cleared. */ }
  document.cookie = "sso_token=; max-age=0; path=/; SameSite=Lax"
}
