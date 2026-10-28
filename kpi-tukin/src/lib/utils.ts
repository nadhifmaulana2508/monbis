import { clsx, type ClassValue } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatNumber(value: unknown, maximumFractionDigits = 2) {
  const number = Number(value)
  if (!Number.isFinite(number)) return "-"
  return new Intl.NumberFormat("id-ID", { maximumFractionDigits }).format(number)
}

export function formatWeight(value: unknown) {
  const number = Number(value)
  if (!Number.isFinite(number)) return "-"
  return `${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 2 }).format(number * 100)}%`
}

export function formatTarget(value: unknown, unit: string) {
  const number = Number(value)
  if (!Number.isFinite(number)) return "-"
  if (unit === "RUPIAH") {
    const absolute = Math.abs(number)
    const compact = (divider: number, suffix: string) => `Rp ${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 2 }).format(number / divider)} ${suffix}`
    if (absolute >= 1_000_000_000_000) return compact(1_000_000_000_000, "T")
    if (absolute >= 1_000_000_000) return compact(1_000_000_000, "M")
    if (absolute >= 1_000_000) return compact(1_000_000, "Jt")
    if (absolute >= 1_000) return compact(1_000, "Rb")
    return `Rp ${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(number)}`
  }
  if (unit === "PERSEN") {
    return new Intl.NumberFormat("id-ID", { style: "percent", maximumFractionDigits: 2 }).format(number)
  }
  return formatNumber(number)
}

export function getToken() {
  const match = document.cookie.match(/(?:^|;\s*)sso_token=([^;]+)/)
  if (match) {
    try { return decodeURIComponent(match[1]) } catch { return match[1] }
  }

  try {
    const direct = String(window.AUTH_TOKEN || localStorage.getItem("dpk_token") || "").trim()
    if (direct) return direct
  } catch {
    // Cookie authentication remains available when storage is restricted.
  }
  return ""
}
