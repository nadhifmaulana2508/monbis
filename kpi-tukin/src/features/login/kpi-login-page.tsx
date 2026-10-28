import { useState, type FormEvent } from "react"
import { BarChart3, Eye, EyeOff, LoaderCircle, ShieldCheck } from "lucide-react"

import { Button } from "@/components/ui/button"
import { loginWithMonbis, type MonbisUser } from "@/lib/auth"

type KpiLoginPageProps = { onAuthenticated: (user: MonbisUser) => void }

export function KpiLoginPage({ onAuthenticated }: KpiLoginPageProps) {
  const [employeeId, setEmployeeId] = useState("")
  const [password, setPassword] = useState("")
  const [showPassword, setShowPassword] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState("")

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    setSubmitting(true)
    setError("")
    try {
      const user = await loginWithMonbis(employeeId, password)
      onAuthenticated(user)
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Login belum berhasil. Coba lagi.")
      setSubmitting(false)
    }
  }

  return (
    <main className="relative grid min-h-[100dvh] place-items-center overflow-y-auto bg-[var(--page)] px-4 py-8 text-[var(--ink)]">
      <div aria-hidden="true" className="pointer-events-none absolute inset-0 overflow-hidden">
        <div className="absolute -left-32 -top-36 size-[420px] rounded-full bg-[color-mix(in_srgb,var(--accent)_8%,transparent)] blur-3xl" />
        <div className="absolute -bottom-48 -right-36 size-[480px] rounded-full bg-[color-mix(in_srgb,var(--neon-green)_7%,transparent)] blur-3xl" />
      </div>

      <section className="relative w-full max-w-[420px] rounded-xl border border-[var(--line)] bg-[var(--surface)] p-6 shadow-[0_18px_60px_rgba(24,24,27,0.08)] sm:p-8">
        <div className="mb-7 flex items-center gap-3">
          <span className="grid size-11 shrink-0 place-items-center rounded-lg bg-[var(--ink)] text-[var(--surface)]">
            <BarChart3 size={21} strokeWidth={2} aria-hidden="true" />
          </span>
          <div>
            <p className="text-sm font-semibold tracking-tight">MONBIS</p>
            <p className="text-xs text-[var(--muted)]">Kinerja & Tukin</p>
          </div>
        </div>

        <div className="mb-6">
          <p className="mb-2 text-[11px] font-semibold uppercase tracking-[0.13em] text-[var(--accent)]">Portal KPI</p>
          <h1 className="text-2xl font-semibold tracking-tight">Masuk ke KPI Tukin</h1>
          <p className="mt-2 text-sm leading-6 text-[var(--muted)]">Gunakan ID pegawai dan password MONBIS untuk melanjutkan.</p>
        </div>

        <form onSubmit={submit} className="space-y-4">
          <div className="space-y-1.5">
            <label htmlFor="kpi-employee-id" className="text-xs font-medium">ID Pegawai</label>
            <input
              id="kpi-employee-id"
              name="employee_id"
              autoComplete="username"
              required
              value={employeeId}
              onChange={(event) => setEmployeeId(event.target.value)}
              className="h-10 w-full rounded-md border border-[var(--line)] bg-[var(--surface)] px-3 text-sm outline-none transition focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]"
              placeholder="Contoh: 102-119"
            />
          </div>

          <div className="space-y-1.5">
            <label htmlFor="kpi-password" className="text-xs font-medium">Password</label>
            <div className="relative">
              <input
                id="kpi-password"
                name="password"
                type={showPassword ? "text" : "password"}
                autoComplete="current-password"
                required
                value={password}
                onChange={(event) => setPassword(event.target.value)}
                className="h-10 w-full rounded-md border border-[var(--line)] bg-[var(--surface)] px-3 pr-11 text-sm outline-none transition focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]"
                placeholder="Masukkan password MONBIS"
              />
              <button
                type="button"
                onClick={() => setShowPassword((shown) => !shown)}
                aria-label={showPassword ? "Sembunyikan password" : "Tampilkan password"}
                className="absolute inset-y-0 right-0 grid w-10 place-items-center rounded-r-md text-[var(--muted)] outline-none hover:text-[var(--ink)] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--ring)]"
              >
                {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
              </button>
            </div>
          </div>

          {error && <p role="alert" className="rounded-md border border-red-500/25 bg-red-500/5 px-3 py-2 text-xs leading-5 text-red-700 dark:text-red-300">{error}</p>}

          <Button type="submit" disabled={submitting} className="h-10 w-full font-semibold">
            {submitting ? <><LoaderCircle size={15} className="animate-spin" aria-hidden="true" /> Memverifikasi...</> : "Masuk ke KPI"}
          </Button>
        </form>

        <div className="mt-6 flex items-start gap-2 border-t border-[var(--line-soft)] pt-4 text-[11px] leading-5 text-[var(--muted)]">
          <ShieldCheck size={15} className="mt-0.5 shrink-0" aria-hidden="true" />
          <p>Autentikasi menggunakan layanan SSO MONBIS. Sesi diverifikasi sebelum data KPI dibuka.</p>
        </div>
      </section>
    </main>
  )
}
