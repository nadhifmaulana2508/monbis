const LOCAL_API_BASE_URL = "http://localhost/report-dpk/api"

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
  if (isLocalHost && (import.meta.env.DEV || (import.meta.env.PROD && window.location.port === "4173"))) return "/report-dpk/api"
  if (import.meta.env.DEV) return "/api"

  return isLocalHost ? LOCAL_API_BASE_URL : `${window.location.origin}/api`
}

export function apiEndpoint(request: string) {
  const base = getApiBaseUrl()
  return `${base}/${request.replace(/^\/+/, "")}/`
}
