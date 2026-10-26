export type UiDensity = "compact" | "comfortable" | "spacious"
export type FontScale = "small" | "default" | "large"

export type AppearancePreferences = {
  density: UiDensity
  fontScale: FontScale
}

export type CurrentUser = {
  key: string
  name: string
  username: string
  office: string
  initials: string
}

const defaultPreferences: AppearancePreferences = {
  density: "comfortable",
  fontScale: "default",
}

type UserSnapshot = Record<string, unknown>

function getUserSnapshot(): UserSnapshot | null {
  if (typeof window === "undefined") return null
  const getUser = (window as Window & { getUser?: () => UserSnapshot | null }).getUser
  const user = getUser?.()
  return user && typeof user === "object" ? user : null
}

function firstText(user: UserSnapshot | null, keys: string[]) {
  for (const key of keys) {
    const value = user?.[key]
    if (typeof value === "string" && value.trim()) return value.trim()
  }
  return ""
}

function makeInitials(name: string) {
  const words = name.split(/\s+/).filter(Boolean)
  if (words.length === 0) return "U"
  return words.slice(0, 2).map((word) => word[0]).join("").toUpperCase()
}

export function getCurrentUser(): CurrentUser {
  const user = getUserSnapshot()
  const username = firstText(user, ["username", "account_handle", "user", "login", "employee_id", "nik", "nip"])
  const name = firstText(user, ["nama_lengkap", "nama", "name", "display_name", "full_name", "ao_name"]) || username || "Pengguna MONBIS"
  const office = firstText(user, ["nama_kantor", "kantor", "office_name", "kode_kantor", "kode_cabang", "kode"]) || "Kantor belum terdeteksi"
  const key = firstText(user, ["id", "pegawai_id", "employee_id", "user_id", "username", "nik", "nip", "kode_kantor"]) || "default"

  return {
    key: key.toLowerCase().replace(/\s+/g, "-"),
    name,
    username: username || "Akun MONBIS",
    office,
    initials: makeInitials(name),
  }
}

function preferenceKey(userKey: string) {
  return `monbis-appearance:${userKey}`
}

export function loadAppearancePreferences(userKey: string): AppearancePreferences {
  if (typeof window === "undefined") return defaultPreferences
  try {
    const stored = JSON.parse(window.localStorage.getItem(preferenceKey(userKey)) || "null") as Partial<AppearancePreferences> | null
    return {
      density: stored?.density === "compact" || stored?.density === "spacious" ? stored.density : defaultPreferences.density,
      fontScale: stored?.fontScale === "small" || stored?.fontScale === "large" ? stored.fontScale : defaultPreferences.fontScale,
    }
  } catch {
    return defaultPreferences
  }
}

export function saveAppearancePreferences(userKey: string, preferences: AppearancePreferences) {
  if (typeof window !== "undefined") window.localStorage.setItem(preferenceKey(userKey), JSON.stringify(preferences))
}

export { defaultPreferences }
