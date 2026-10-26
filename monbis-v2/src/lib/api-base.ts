const LOCAL_API_BASE_URL = "http://localhost/report-dpk/api"
const PRODUCTION_API_BASE_URL = "https://monbis.bkkjateng.co.id/api"

function trimTrailingSlash(value: string) {
  return value.replace(/\/+$/, "")
}

export function getApiBaseUrl() {
  const configured = import.meta.env.VITE_API_BASE_URL?.trim()
  if (configured) return trimTrailingSlash(configured)

  const hostname = typeof window === "undefined"
    ? "localhost"
    : window.location.hostname.toLowerCase()

  const isLocalHost = hostname === "localhost" || hostname === "127.0.0.1" || hostname === "::1"
  if (import.meta.env.DEV || (import.meta.env.PROD && isLocalHost && window.location.port === "4173")) return "/api"

  return isLocalHost ? LOCAL_API_BASE_URL : PRODUCTION_API_BASE_URL
}

export function apiEndpoint(request: string) {
  const base = getApiBaseUrl()
  return `${base}/${request.replace(/^\/+/, "")}/`
}
