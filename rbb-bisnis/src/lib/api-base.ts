import { getToken } from "@/lib/utils"

const LOCAL_API = "http://localhost/report-dpk/api"

function isMonbisHost() {
  const hostname = window.location.hostname
  return hostname === "monbis.bkkjateng.co.id" || hostname.endsWith(".monbis.bkkjateng.co.id")
}

function configuredApiBase() {
  const value = String(import.meta.env.VITE_API_BASE_URL || "").trim().replace(/\/$/, "")
  if (value) return value
  if (import.meta.env.DEV) return "/api"
  return isMonbisHost() ? "/api" : LOCAL_API
}

export function apiUrl(path: string) {
  const base = configuredApiBase()
  const cleanPath = path.replace(/^\/+/, "")
  return base === "/api" ? `/api/${cleanPath}` : `${base}/${cleanPath}`
}

async function parseApiResponse<T>(response: Response, fallbackMessage: string) {
  const json = await response.json().catch(() => ({})) as { status?: number; message?: string; data?: T }
  if (!response.ok || Number(json.status) !== 200) {
    throw new Error(json.message || fallbackMessage)
  }
  return (json.data || {}) as T
}

export async function postApi<T>(path: string, payload: Record<string, unknown>, signal?: AbortSignal) {
  const token = getToken()
  const headers: Record<string, string> = { "Content-Type": "application/json", Accept: "application/json" }
  if (token) headers.Authorization = /^Bearer\s/i.test(token) ? token : `Bearer ${token}`

  const response = await fetch(apiUrl(path), {
    method: "POST",
    credentials: "include",
    headers,
    body: JSON.stringify(payload),
    signal,
  })
  return parseApiResponse<T>(response, "Data RBB gagal dimuat")
}
