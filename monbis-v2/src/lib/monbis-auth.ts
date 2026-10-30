import { apiEndpoint } from "@/lib/api-base"

export type MonbisUser = Record<string, unknown>

declare global {
  interface Window {
    AUTH_TOKEN?: string
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
    getUser?: () => { kode?: string; kode_kantor?: string } | null
    __USER?: MonbisUser
  }
}

type AuthEnvelope<T> = { status?: number | string; message?: string; data?: T }

export function readMonbisToken() {
  const cookie = document.cookie.match(/(?:^|;\s*)sso_token=([^;]+)/)
  if (cookie) {
    try { return decodeURIComponent(cookie[1]) } catch { return cookie[1] }
  }
  try { return String(localStorage.getItem("dpk_token") || "").trim() }
  catch { return "" }
}

function readStoredMonbisUser() {
  for (const key of ["dpk_user", "app_user", "user"]) {
    try {
      const value = localStorage.getItem(key)
      if (!value) continue
      const user = JSON.parse(value)
      if (user && typeof user === "object") return user as MonbisUser
    } catch { /* Ignore malformed cached profile. */ }
  }
  return null
}

export function readMonbisUser(): MonbisUser | null {
  if (window.__USER && typeof window.__USER === "object") return window.__USER
  try {
    const user = window.getUser?.()
    if (user && typeof user === "object") return user
  } catch { /* Continue to the known MONBIS local profile. */ }
  return readStoredMonbisUser()
}

function normalizeUser(user: MonbisUser) {
  return {
    ...user,
    role: user.job_position === "Divisi Operasional" || user.unit_kerja === "Divisi Operasional" ? "dev" : "user",
  }
}

async function requestAuth<T>(path: string, init?: RequestInit): Promise<AuthEnvelope<T>> {
  let response: Response
  try {
    response = await fetch(apiEndpoint(`auth/${path}`), {
      ...init,
      credentials: "include",
      headers: { Accept: "application/json", ...(init?.body ? { "Content-Type": "application/json" } : {}), ...init?.headers },
      cache: "no-store",
    })
  } catch {
    throw new Error("Gagal terhubung ke server autentikasi MONBIS.")
  }

  let payload: AuthEnvelope<T>
  try { payload = await response.json() as AuthEnvelope<T> }
  catch { throw new Error("Layanan autentikasi MONBIS memberikan respons yang tidak valid.") }
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || "Permintaan autentikasi gagal.")
  return payload
}

function saveUser(user: MonbisUser) {
  localStorage.setItem("dpk_user", JSON.stringify(user))
  window.__USER = user
}

export function clearMonbisSession() {
  try {
    localStorage.removeItem("dpk_token")
    localStorage.removeItem("dpk_user")
    localStorage.removeItem("dpk_user_verified_at")
  } catch { /* Session cleanup should continue even when storage is blocked. */ }
  document.cookie = "sso_token=; path=/; max-age=0; SameSite=Lax"
  window.__USER = undefined
  void fetch(apiEndpoint("auth/logout"), { method: "POST", credentials: "include", cache: "no-store", keepalive: true }).catch(() => undefined)
}

export async function loginMonbis(employeeId: string, password: string) {
  const normalizedEmployeeId = employeeId.trim()
  if (!normalizedEmployeeId) throw new Error("ID Pegawai wajib diisi.")
  if (!password) throw new Error("Password wajib diisi.")

  const payload = await requestAuth<MonbisUser>("login", {
    method: "POST",
    body: JSON.stringify({ id_peg: normalizedEmployeeId, password }),
  })
  if (!payload.data || typeof payload.data !== "object") throw new Error("Data pegawai tidak ditemukan.")
  const user = normalizeUser(payload.data)
  saveUser(user)
  return user
}

export async function validateMonbisSession() {
  try {
    const token = readMonbisToken()
    const payload = await requestAuth<MonbisUser>("whoami", {
      headers: token ? { Authorization: /^Bearer\s/i.test(token) ? token : `Bearer ${token}` } : {},
    })
    if (!payload.data || typeof payload.data !== "object") throw new Error("Data pegawai tidak ditemukan.")
    const user = normalizeUser(payload.data)
    saveUser(user)
    return user
  } catch {
    clearMonbisSession()
    return null
  }
}

export async function sendPasswordReset(email: string) {
  const normalizedEmail = email.trim()
  if (!normalizedEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalizedEmail)) throw new Error("Masukkan alamat email yang valid.")
  return requestAuth<{ otp_token?: string }>("forgot-password", { method: "POST", body: JSON.stringify({ email: normalizedEmail }) })
}

export async function verifyPasswordOtp(otpToken: string, otpCode: string) {
  const normalizedOtp = otpCode.trim()
  if (!otpToken.trim()) throw new Error("Sesi verifikasi OTP tidak valid. Minta kode baru.")
  if (!/^\d{6}$/.test(normalizedOtp)) throw new Error("Kode OTP harus terdiri dari 6 angka.")
  return requestAuth<{ reset_token?: string }>("verify-otp", { method: "POST", body: JSON.stringify({ otp_token: otpToken, otp_code: normalizedOtp }) })
}

export async function resetMonbisPassword(resetToken: string, newPassword: string) {
  if (!resetToken.trim()) throw new Error("Sesi reset password tidak valid. Ulangi proses reset.")
  if (!newPassword) throw new Error("Password baru wajib diisi.")
  return requestAuth("reset-password", { method: "POST", body: JSON.stringify({ reset_token: resetToken, new_password: newPassword }) })
}

export function installMonbisApiFetch() {
  window.getUser = () => readStoredMonbisUser() as { kode?: string; kode_kantor?: string } | null
  window.apiFetch = async (input, init = {}) => {
    const token = readMonbisToken()
    const headers = new Headers(init.headers)
    headers.set("Accept", headers.get("Accept") || "application/json")
    if (token && !headers.has("Authorization")) headers.set("Authorization", /^Bearer\s/i.test(token) ? token : `Bearer ${token}`)
    const response = await fetch(input, { ...init, credentials: "include", headers })
    if (response.status === 401) window.dispatchEvent(new CustomEvent("monbis:session-expired"))
    return response
  }
}
