import { useEffect, useState, type FormEvent } from "react"
import { BarChart3, Eye, EyeOff, LoaderCircle, ShieldCheck } from "lucide-react"

import { RbbPage } from "@/features/rbb-page"
import { Button } from "@/components/ui/button"
import { loginWithMonbis, validateRbbSession, type RbbUser } from "@/lib/auth"

export default function App() {
  const [user, setUser] = useState<RbbUser | null>(null)
  const [checking, setChecking] = useState(true)
  const [sessionError, setSessionError] = useState("")

  useEffect(() => {
    const controller = new AbortController()
    validateRbbSession(controller.signal).then((identity) => {
      setUser(identity)
      if (!identity) setSessionError("Silakan masuk dengan akun MONBIS untuk membuka RBB.")
    }).catch((reason: unknown) => {
      if ((reason as Error)?.name !== "AbortError") setSessionError("Sesi belum dapat diverifikasi. Silakan masuk kembali.")
    }).finally(() => setChecking(false))
    return () => controller.abort()
  }, [])

  if (checking) return <main className="grid min-h-dvh place-items-center bg-[var(--page)] text-sm text-[var(--muted)]"><span className="inline-flex items-center gap-2"><LoaderCircle size={16} className="animate-spin" /> Memeriksa sesi MONBIS...</span></main>
  if (!user) return <RbbLoginPage onAuthenticated={setUser} initialMessage={sessionError} />
  return <RbbPage />
}

function RbbLoginPage({ onAuthenticated, initialMessage }: { onAuthenticated: (user: RbbUser) => void; initialMessage: string }) {
  const [employeeId, setEmployeeId] = useState("")
  const [password, setPassword] = useState("")
  const [showPassword, setShowPassword] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState(initialMessage)

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    setSubmitting(true)
    setError("")
    try {
      const identity = await loginWithMonbis(employeeId, password)
      onAuthenticated(identity)
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Login belum berhasil. Silakan coba lagi.")
      setSubmitting(false)
    }
  }

  return (
    <main className="relative grid min-h-dvh place-items-center overflow-y-auto bg-[var(--page)] px-4 py-8 text-[var(--ink)]">
      <section className="w-full max-w-[420px] rounded-xl border border-[var(--line)] bg-[var(--surface)] p-6 shadow-[0_18px_60px_rgba(24,24,27,0.08)] sm:p-8">
        <div className="mb-7 flex items-center gap-3">
          <span className="grid size-11 shrink-0 place-items-center rounded-lg bg-[var(--ink)] text-[var(--surface)]"><BarChart3 size={21} aria-hidden="true" /></span>
          <div><p className="text-sm font-semibold tracking-tight">MONBIS</p><p className="text-xs text-[var(--muted)]">RBB Bisnis</p></div>
        </div>
        <div className="mb-6">
          <p className="mb-2 text-[11px] font-semibold uppercase tracking-[0.13em] text-[var(--accent)]">Portal RBB</p>
          <h1 className="text-2xl font-semibold tracking-tight">Masuk ke RBB Bisnis</h1>
          <p className="mt-2 text-sm leading-6 text-[var(--muted)]">Gunakan ID pegawai dan password MONBIS untuk melanjutkan.</p>
        </div>
        <form onSubmit={submit} className="space-y-4">
          <div className="space-y-1.5"><label htmlFor="rbb-employee-id" className="text-xs font-medium">ID Pegawai</label><input id="rbb-employee-id" name="employee_id" autoComplete="username" required value={employeeId} onChange={(event) => setEmployeeId(event.target.value)} className="h-10 w-full rounded-md border border-[var(--line)] bg-[var(--surface)] px-3 text-sm outline-none focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]" placeholder="Contoh: 102-119" /></div>
          <div className="space-y-1.5"><label htmlFor="rbb-password" className="text-xs font-medium">Password</label><div className="relative"><input id="rbb-password" name="password" type={showPassword ? "text" : "password"} autoComplete="current-password" required value={password} onChange={(event) => setPassword(event.target.value)} className="h-10 w-full rounded-md border border-[var(--line)] bg-[var(--surface)] px-3 pr-11 text-sm outline-none focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]" placeholder="Masukkan password MONBIS" /><button type="button" onClick={() => setShowPassword((shown) => !shown)} aria-label={showPassword ? "Sembunyikan password" : "Tampilkan password"} className="absolute inset-y-0 right-0 grid w-10 place-items-center rounded-r-md text-[var(--muted)] hover:text-[var(--ink)] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--ring)]">{showPassword ? <EyeOff size={16} /> : <Eye size={16} />}</button></div></div>
          {error && <p role="alert" className="rounded-md border border-red-500/25 bg-red-500/5 px-3 py-2 text-xs leading-5 text-red-700">{error}</p>}
          <Button type="submit" disabled={submitting} className="h-10 w-full font-semibold">{submitting ? <><LoaderCircle size={15} className="animate-spin" aria-hidden="true" /> Memverifikasi...</> : "Masuk ke RBB"}</Button>
        </form>
        <div className="mt-6 flex items-start gap-2 border-t border-[var(--line-soft)] pt-4 text-[11px] leading-5 text-[var(--muted)]"><ShieldCheck size={15} className="mt-0.5 shrink-0" aria-hidden="true" /><p>Login diverifikasi melalui SSO MONBIS sebelum data RBB dibuka.</p></div>
      </section>
    </main>
  )
}
