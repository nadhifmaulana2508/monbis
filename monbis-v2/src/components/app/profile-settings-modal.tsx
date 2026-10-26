import { Monitor, Type, UserRound, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import type { AppearancePreferences, CurrentUser, FontScale, UiDensity } from "@/lib/user-preferences"

type ProfileSettingsModalProps = {
  open: boolean
  user: CurrentUser
  preferences: AppearancePreferences
  onChange: (preferences: AppearancePreferences) => void
  onReset: () => void
  onClose: () => void
}

export function ProfileSettingsModal({ open, user, preferences, onChange, onReset, onClose }: ProfileSettingsModalProps) {
  if (!open) return null

  return (
    <div className="fixed inset-0 z-50 grid place-items-center bg-foreground/35 p-4">
      <button type="button" aria-label="Tutup profil dan pengaturan" className="absolute inset-0 cursor-default" onClick={onClose} />
      <section role="dialog" aria-modal="true" aria-labelledby="profile-settings-title" className="relative flex max-h-[min(90vh,680px)] w-full max-w-lg flex-col overflow-hidden rounded-xl border bg-background shadow-2xl">
        <header className="flex items-start justify-between gap-3 border-b bg-muted/20 px-4 py-3 sm:px-6 sm:py-4">
          <div className="flex min-w-0 items-start gap-3">
            <span className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary text-xs font-semibold text-primary-foreground">{user.initials}</span>
            <div className="min-w-0">
              <h2 id="profile-settings-title" className="truncate text-base font-semibold tracking-tight sm:text-lg">Profil &amp; tampilan</h2>
              <p className="mt-0.5 text-xs text-muted-foreground">Preferensi ini hanya berlaku untuk akun yang sedang login.</p>
            </div>
          </div>
          <Button type="button" variant="ghost" size="icon" className="size-9 shrink-0" aria-label="Tutup profil dan pengaturan" onClick={onClose}>
            <X aria-hidden="true" />
          </Button>
        </header>

        <div className="min-h-0 overflow-auto p-4 sm:p-6">
          <div className="flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
            <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"><UserRound className="size-5" aria-hidden="true" /></span>
            <div className="min-w-0">
              <p className="truncate text-sm font-semibold">{user.name}</p>
              <p className="truncate text-xs text-muted-foreground">{user.username} · {user.office}</p>
            </div>
          </div>

          <div className="mt-5 grid gap-4">
            <label className="grid gap-1.5 text-sm font-medium" htmlFor="ui-density">
              <span className="flex items-center gap-2"><Monitor className="size-4 text-muted-foreground" aria-hidden="true" /> Ukuran tampilan</span>
              <select id="ui-density" value={preferences.density} onChange={(event) => onChange({ ...preferences, density: event.target.value as UiDensity })} className="h-10 rounded-lg border bg-background px-3 text-sm font-normal outline-none focus-visible:ring-3 focus-visible:ring-ring/50">
                <option value="compact">Rapat</option>
                <option value="comfortable">Normal</option>
                <option value="spacious">Lega</option>
              </select>
              <span className="text-xs font-normal text-muted-foreground">Mengatur ruang antar elemen dan area kerja halaman.</span>
            </label>

            <label className="grid gap-1.5 text-sm font-medium" htmlFor="font-scale">
              <span className="flex items-center gap-2"><Type className="size-4 text-muted-foreground" aria-hidden="true" /> Ukuran teks</span>
              <select id="font-scale" value={preferences.fontScale} onChange={(event) => onChange({ ...preferences, fontScale: event.target.value as FontScale })} className="h-10 rounded-lg border bg-background px-3 text-sm font-normal outline-none focus-visible:ring-3 focus-visible:ring-ring/50">
                <option value="small">Kecil</option>
                <option value="default">Normal</option>
                <option value="large">Besar</option>
              </select>
              <span className="text-xs font-normal text-muted-foreground">Ukuran teks diterapkan ke semua menu React.</span>
            </label>
          </div>
        </div>

        <footer className="flex items-center justify-between gap-3 border-t px-4 py-3 sm:px-6">
          <Button type="button" variant="ghost" size="sm" onClick={onReset}>Kembalikan default</Button>
          <Button type="button" variant="outline" size="sm" onClick={onClose}>Selesai</Button>
        </footer>
      </section>
    </div>
  )
}
