import { useEffect, type ReactNode } from "react"
import { AlertCircle, Check, LoaderCircle, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

export function KpiPage({ eyebrow = "KPI Tukin", title, description, actions, children }: {
  eyebrow?: string
  title: string
  description: string
  actions?: ReactNode
  children: ReactNode
}) {
  return (
    <div className="mx-auto w-full max-w-[1500px] space-y-5 p-4 sm:p-6 lg:p-8">
      <header className="flex flex-wrap items-start justify-between gap-4">
        <div className="min-w-0">
          <p className="text-xs font-medium uppercase tracking-[0.14em] text-[var(--muted)]">{eyebrow}</p>
          <h2 className="mt-2 text-xl font-semibold tracking-tight sm:text-2xl">{title}</h2>
          <p className="mt-1 max-w-3xl text-sm leading-6 text-[var(--muted)]">{description}</p>
        </div>
        {actions && <div className="flex shrink-0 items-center gap-2">{actions}</div>}
      </header>
      {children}
    </div>
  )
}

export function KpiPanel({ title, description, actions, children, className }: {
  title?: string
  description?: string
  actions?: ReactNode
  children: ReactNode
  className?: string
}) {
  return (
    <section className={cn("overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--surface)]", className)}>
      {(title || description || actions) && <div className="flex flex-wrap items-start justify-between gap-3 border-b border-[var(--line)] px-4 py-3 sm:px-5">
        <div>{title && <h3 className="font-semibold">{title}</h3>}{description && <p className="mt-1 text-xs leading-5 text-[var(--muted)]">{description}</p>}</div>
        {actions && <div className="flex items-center gap-2">{actions}</div>}
      </div>}
      {children}
    </section>
  )
}

export function KpiField({ label, children, className }: { label: string; children: ReactNode; className?: string }) {
  return <label className={cn("grid gap-1.5 text-xs font-medium text-[var(--muted)]", className)}><span>{label}</span>{children}</label>
}

export function KpiSelect({ label, value, onChange, children, className, disabled }: { label: string; value: string; onChange: (value: string) => void; children: ReactNode; className?: string; disabled?: boolean }) {
  return <KpiField label={label} className={className}><select disabled={disabled} value={value} onChange={(event) => onChange(event.target.value)} className="h-9 min-w-0 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-sm text-[var(--ink)] outline-none focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)] disabled:opacity-50">{children}</select></KpiField>
}

export function KpiInput({ label, value, onChange, placeholder, type = "text", className }: { label: string; value: string; onChange: (value: string) => void; placeholder?: string; type?: string; className?: string }) {
  return <KpiField label={label} className={className}><input type={type} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} className="h-9 min-w-0 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-sm text-[var(--ink)] outline-none placeholder:text-[var(--muted)] focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]" /></KpiField>
}

export function KpiMetric({ label, value, detail, tone = "default" }: { label: string; value: ReactNode; detail?: ReactNode; tone?: "default" | "accent" | "success" | "warning" }) {
  return <div className="rounded-lg border border-[var(--line)] bg-[var(--surface-subtle)] px-4 py-3"><p className="text-xs text-[var(--muted)]">{label}</p><p className={cn("mt-1 font-semibold tabular-nums", tone === "accent" && "text-[var(--accent)]", tone === "success" && "text-emerald-600 dark:text-emerald-400", tone === "warning" && "text-amber-600 dark:text-amber-400")}>{value}</p>{detail && <p className="mt-1 text-[11px] leading-4 text-[var(--muted)]">{detail}</p>}</div>
}

export function KpiState({ loading, error, empty, onRetry, children }: { loading?: boolean; error?: string; empty?: string; onRetry?: () => void; children: ReactNode }) {
  if (loading) return <div className="grid min-h-52 place-items-center gap-2 px-6 text-center text-sm text-[var(--muted)]"><LoaderCircle size={18} className="animate-spin text-[var(--accent)]" /><span>Mengambil data dari MONBIS...</span></div>
  if (error) return <div className="grid min-h-52 place-items-center gap-3 px-6 text-center"><AlertCircle size={20} className="text-red-600 dark:text-red-400" /><p className="max-w-lg text-sm text-red-600 dark:text-red-400">{error}</p>{onRetry && <Button variant="outline" size="sm" onClick={onRetry}>Coba lagi</Button>}</div>
  if (empty) return <div className="grid min-h-52 place-items-center px-6 text-center text-sm text-[var(--muted)]">{empty}</div>
  return <>{children}</>
}

export function KpiStatus({ children, tone = "neutral" }: { children: ReactNode; tone?: "neutral" | "success" | "warning" | "danger" }) {
  return <span className={cn("inline-flex items-center gap-1 rounded-md border px-2 py-1 text-[11px] font-medium", tone === "success" && "border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-300", tone === "warning" && "border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-300", tone === "danger" && "border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300", tone === "neutral" && "border-[var(--line)] text-[var(--muted)]")}>{tone === "success" && <Check size={12} />}{children}</span>
}

export function KpiModal({ open, title, description, onClose, children }: { open: boolean; title: string; description?: string; onClose: () => void; children: ReactNode }) {
  useEffect(() => {
    if (!open) return undefined
    const onKeyDown = (event: KeyboardEvent) => event.key === "Escape" && onClose()
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [onClose, open])

  if (!open) return null
  return <div className="fixed inset-0 z-50 grid place-items-center bg-black/40 p-4" role="presentation" onMouseDown={onClose}><section className="max-h-[min(760px,calc(100dvh-32px))] w-full max-w-4xl overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--surface)] shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="kpi-modal-title" onMouseDown={(event) => event.stopPropagation()}><div className="flex items-start justify-between gap-4 border-b border-[var(--line-soft)] px-5 py-4"><div><h2 id="kpi-modal-title" className="font-semibold">{title}</h2>{description && <p className="mt-1 text-xs text-[var(--muted)]">{description}</p>}</div><Button variant="ghost" size="icon" aria-label="Tutup dialog" onClick={onClose}><X size={16} /></Button></div><div className="max-h-[calc(100dvh-132px)] overflow-auto px-5 py-5">{children}</div></section></div>
}
