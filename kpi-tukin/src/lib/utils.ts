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
    return `Rp ${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(number)}`
  }
  if (unit === "PERSEN") {
    return new Intl.NumberFormat("id-ID", { style: "percent", maximumFractionDigits: 2 }).format(number)
  }
  return formatNumber(number)
}

export function getToken() {
  try {
    const direct = String(window.AUTH_TOKEN || localStorage.getItem("dpk_token") || "").trim()
    if (direct) return direct
  } catch {
    // Cookie authentication remains available when storage is restricted.
  }

  const match = document.cookie.match(/(?:^|;\s*)sso_token=([^;]+)/)
  if (!match) return ""
  try {
    return decodeURIComponent(match[1])
  } catch {
    return match[1]
  }
}
