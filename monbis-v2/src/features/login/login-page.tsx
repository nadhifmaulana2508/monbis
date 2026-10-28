import { useEffect, useRef, useState, type FormEvent } from "react"
import { Eye, EyeOff, LoaderCircle, ShieldCheck, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { loginMonbis, resetMonbisPassword, sendPasswordReset, verifyPasswordOtp, type MonbisUser } from "@/lib/monbis-auth"

type LoginPageProps = { onLogin: (user: MonbisUser) => void }
type ResetStep = 1 | 2 | 3 | "done"

const fieldClass = "h-10 w-full rounded-md border border-input bg-background px-3 text-sm text-foreground shadow-xs outline-none transition placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/30"

export function LoginPage({ onLogin }: LoginPageProps) {
  const [employeeId, setEmployeeId] = useState("")
  const [password, setPassword] = useState("")
  const [showPassword, setShowPassword] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState("")
  const [resetOpen, setResetOpen] = useState(false)
  const [resetStep, setResetStep] = useState<ResetStep>(1)
  const [email, setEmail] = useState("")
  const [otp, setOtp] = useState("")
  const [newPassword, setNewPassword] = useState("")
  const [otpToken, setOtpToken] = useState("")
  const [resetToken, setResetToken] = useState("")
  const [resetBusy, setResetBusy] = useState(false)
  const [resetError, setResetError] = useState("")
  const [resetNotice, setResetNotice] = useState("")
  const dialogRef = useRef<HTMLDivElement>(null)
  const forgotButtonRef = useRef<HTMLButtonElement>(null)

  useEffect(() => {
    if (!resetOpen) return undefined
    dialogRef.current?.focus()
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape" && !resetBusy) closeReset()
    }
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [resetOpen, resetBusy])

  function closeReset() {
    setResetOpen(false)
    window.setTimeout(() => forgotButtonRef.current?.focus(), 0)
  }

  async function submitLogin(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    const normalizedEmployeeId = employeeId.trim()
    if (!normalizedEmployeeId || !password) {
      setError(!normalizedEmployeeId ? "ID Pegawai wajib diisi." : "Password wajib diisi.")
      return
    }
    setSubmitting(true)
    setError("")
    try { onLogin(await loginMonbis(normalizedEmployeeId, password)) }
    catch (reason) { setError(reason instanceof Error ? reason.message : "Login belum berhasil. Silakan coba lagi.") }
    finally { setSubmitting(false) }
  }

  function openReset() {
    setResetStep(1); setEmail(""); setOtp(""); setNewPassword(""); setOtpToken(""); setResetToken(""); setResetError(""); setResetNotice("")
    setResetOpen(true)
  }

  async function submitReset(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    if (resetStep === 1 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
      setResetError("Masukkan alamat email yang valid.")
      return
    }
    if (resetStep === 2 && !/^\d{6}$/.test(otp.trim())) {
      setResetError("Kode OTP harus terdiri dari 6 angka.")
      return
    }
    if (resetStep === 3 && !newPassword) {
      setResetError("Password baru wajib diisi.")
      return
    }
    setResetBusy(true); setResetError(""); setResetNotice("")
    try {
      if (resetStep === 1) {
        const response = await sendPasswordReset(email)
        if (!response.data?.otp_token) throw new Error("Token OTP tidak ditemukan pada respons SSO.")
        setOtpToken(response.data.otp_token); setResetNotice("Kode OTP berhasil dikirim ke email Anda."); setResetStep(2)
      } else if (resetStep === 2) {
        const response = await verifyPasswordOtp(otpToken, otp)
        if (!response.data?.reset_token) throw new Error("Token reset tidak ditemukan pada respons SSO.")
        setResetToken(response.data.reset_token); setResetNotice("OTP valid. Silakan buat password baru."); setResetStep(3)
      } else if (resetStep === 3) {
        await resetMonbisPassword(resetToken, newPassword)
        setResetNotice("Password berhasil diubah. Silakan login dengan password baru."); setResetStep("done")
      }
    } catch (reason) { setResetError(reason instanceof Error ? reason.message : "Permintaan reset password gagal.") }
    finally { setResetBusy(false) }
  }

  return <main className="flex min-h-[100dvh] items-center justify-center overflow-y-auto bg-muted/35 px-4 py-8 sm:px-6">
    <div className="w-full max-w-[440px]">
      <div className="mb-5 flex items-center gap-3 px-1">
        <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-sm font-bold text-primary-foreground" aria-hidden="true">M</span>
        <div><p className="text-sm font-semibold tracking-tight">MONBIS</p><p className="text-xs text-muted-foreground">Laporan bisnis</p></div>
      </div>
      <Card className="gap-0 rounded-xl border bg-card shadow-sm">
        <CardHeader className="gap-1.5 px-5 pb-4 pt-5 sm:px-6 sm:pt-6">
          <p className="text-xs font-medium text-muted-foreground">Portal Internal MONBIS</p>
          <CardTitle className="text-xl font-semibold tracking-tight">Login Pegawai</CardTitle>
          <CardDescription>Masukkan ID Pegawai dan password MONBIS Anda.</CardDescription>
        </CardHeader>
        <CardContent className="px-5 pb-5 sm:px-6 sm:pb-6">
          <form className="space-y-4" onSubmit={submitLogin}>
            <label className="grid gap-1.5 text-sm font-medium" htmlFor="monbis-employee-id">ID Pegawai
              <input id="monbis-employee-id" className={fieldClass} value={employeeId} onChange={(event) => setEmployeeId(event.target.value)} autoComplete="username" required />
            </label>
            <div className="grid gap-1.5 text-sm font-medium">
              <div className="flex items-center justify-between gap-3"><label htmlFor="monbis-password">Password</label><button ref={forgotButtonRef} type="button" onClick={openReset} className="min-h-8 rounded-sm text-xs font-medium text-primary underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">Lupa password?</button></div>
              <div className="relative">
                <input id="monbis-password" className={`${fieldClass} pr-11`} type={showPassword ? "text" : "password"} value={password} onChange={(event) => setPassword(event.target.value)} autoComplete="current-password" required />
                <Button type="button" variant="ghost" size="icon" className="absolute right-0 top-0 size-10 text-muted-foreground" onClick={() => setShowPassword((shown) => !shown)} aria-label={showPassword ? "Sembunyikan password" : "Tampilkan password"}>{showPassword ? <EyeOff aria-hidden="true" /> : <Eye aria-hidden="true" />}</Button>
              </div>
            </div>
            {error && <p role="alert" className="rounded-md border border-destructive/30 bg-destructive/5 px-3 py-2 text-sm text-destructive">{error}</p>}
            <Button type="submit" className="h-10 w-full" disabled={submitting}>{submitting ? <><LoaderCircle className="animate-spin" aria-hidden="true" />Memeriksa sesi...</> : "Verifikasi & Masuk"}</Button>
          </form>
          <div className="mt-5 flex items-start gap-2 border-t pt-4 text-xs leading-relaxed text-muted-foreground"><ShieldCheck className="mt-0.5 size-4 shrink-0" aria-hidden="true" /><p>Gunakan akun MONBIS Anda. Sesi akan diverifikasi sebelum laporan dibuka.</p></div>
        </CardContent>
      </Card>
    </div>
    {resetOpen && <div className="fixed inset-0 z-[100] grid place-items-center bg-black/45 px-4 py-6" onMouseDown={(event) => { if (event.target === event.currentTarget && !resetBusy) closeReset() }}>
      <div ref={dialogRef} role="dialog" aria-modal="true" aria-labelledby="reset-title" tabIndex={-1} className="w-full max-w-md rounded-xl border bg-background shadow-xl outline-none">
        <header className="flex items-center justify-between border-b px-4 py-3.5 sm:px-5"><div><h2 id="reset-title" className="text-base font-semibold">Reset Password</h2><p className="mt-0.5 text-xs text-muted-foreground">{resetStep === 1 ? "Kirim kode OTP ke email terdaftar." : resetStep === 2 ? "Masukkan kode OTP yang dikirim ke email." : resetStep === 3 ? "Buat password baru untuk akun Anda." : "Password akun telah diperbarui."}</p></div><Button type="button" variant="ghost" size="icon" onClick={closeReset} aria-label="Tutup dialog"><X aria-hidden="true" /></Button></header>
        <div className="space-y-3 p-4 sm:p-5">
          {resetNotice && <p role="status" className="rounded-md border border-emerald-600/30 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-300">{resetNotice}</p>}
          {resetError && <p role="alert" className="rounded-md border border-destructive/30 bg-destructive/5 px-3 py-2 text-sm text-destructive">{resetError}</p>}
          {resetStep !== "done" ? <form className="space-y-4" onSubmit={submitReset}>
            {resetStep === 1 && <label className="grid gap-1.5 text-sm font-medium" htmlFor="reset-email">Email terdaftar<input id="reset-email" className={fieldClass} type="email" value={email} onChange={(event) => setEmail(event.target.value)} autoComplete="email" required /></label>}
            {resetStep === 2 && <label className="grid gap-1.5 text-sm font-medium" htmlFor="reset-otp">Kode OTP<input id="reset-otp" className={`${fieldClass} text-center tracking-[0.35em]`} inputMode="numeric" maxLength={6} value={otp} onChange={(event) => setOtp(event.target.value.replace(/\D/g, ""))} required /></label>}
            {resetStep === 3 && <label className="grid gap-1.5 text-sm font-medium" htmlFor="reset-new-password">Password baru<input id="reset-new-password" className={fieldClass} type="password" autoComplete="new-password" value={newPassword} onChange={(event) => setNewPassword(event.target.value)} required /></label>}
            <Button type="submit" className="h-10 w-full" disabled={resetBusy}>{resetBusy ? <><LoaderCircle className="animate-spin" aria-hidden="true" />Memproses...</> : resetStep === 1 ? "Kirim OTP" : resetStep === 2 ? "Verifikasi OTP" : "Simpan Password Baru"}</Button>
          </form> : <Button type="button" className="h-10 w-full" onClick={closeReset}>Selesai & Login</Button>}
        </div>
      </div>
    </div>}
  </main>
}
