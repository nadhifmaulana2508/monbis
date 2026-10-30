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

type ApiEnvelope<T> = { status?: number | string; message?: string; data?: T }

async function requestAppApi<T>(path: string, init?: RequestInit) {
  let response: Response
  try {
    response = await fetch(`/api/auth/${path}`, {
      cache: "no-store",
      credentials: "include",
      ...init,
    })
  } catch {
    throw new Error("Gagal menghubungi API MONBIS. Pastikan Apache/PHP lokal sedang berjalan lalu coba lagi.")
  }
  const payload = await response.json().catch(() => ({})) as ApiEnvelope<T>
  if (!response.ok || Number(payload.status) !== 200 || !payload.data) {
    throw new Error(payload.message || "Sesi MONBIS tidak valid. Silakan masuk kembali.")
  }
  return payload.data
}

export async function loginWithMonbis(employeeId: string, password: string) {
  const user = await requestAppApi<RbbUser>("login", {
    method: "POST",
    headers: { Accept: "application/json", "Content-Type": "application/json" },
    body: JSON.stringify({ id_peg: employeeId.trim(), password, app: "monbis" }),
  })
  if (!user.employee_id) throw new Error("Data pegawai belum dapat diverifikasi.")
  return user
}

export async function validateRbbSession(signal?: AbortSignal) {
  try {
    const user = await requestAppApi<RbbUser>("whoami", {
      headers: { Accept: "application/json" },
      signal,
    })
    if (!user.employee_id) return null
    return user
  } catch (error) {
    if (error instanceof DOMException && error.name === "AbortError") throw error
    return null
  }
}

export function clearRbbSession() {
  return requestAppApi<unknown>("logout", {
    method: "POST",
    headers: { Accept: "application/json" },
  })
}
