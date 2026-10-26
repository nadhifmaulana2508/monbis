import { useEffect, useId, useRef, type ReactNode } from "react"
import { Info } from "lucide-react"

import { ReportDetailLayer } from "@/components/report-detail-layer"
import { ReportModalCloseButton } from "@/components/report-modal-close-button"

type Props = {
  open: boolean
  title: string
  description?: string
  meta?: ReactNode
  onClose: () => void
  children: ReactNode
  className?: string
}

export function ReportInfoDialog({ open, title, description, meta, onClose, children, className = "" }: Props) {
  const titleId = useId()
  const descriptionId = useId()
  const closeButtonRef = useRef<HTMLButtonElement>(null)
  const onCloseRef = useRef(onClose)

  useEffect(() => {
    onCloseRef.current = onClose
  }, [onClose])

  useEffect(() => {
    if (!open) return undefined
    const previousFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null
    const previousOverflow = document.body.style.overflow
    document.body.style.overflow = "hidden"
    closeButtonRef.current?.focus()

    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") onCloseRef.current()
    }
    window.addEventListener("keydown", onKeyDown)
    return () => {
      window.removeEventListener("keydown", onKeyDown)
      document.body.style.overflow = previousOverflow
      previousFocus?.focus()
    }
  }, [open])

  if (!open) return null

  return (
    <ReportDetailLayer className="grid place-items-center p-4">
      <button type="button" aria-label="Tutup dialog informasi" className="absolute inset-0 bg-black/55" onClick={onClose} />
      <section
        role="dialog"
        aria-modal="true"
        aria-labelledby={titleId}
        aria-describedby={description ? descriptionId : undefined}
        className={`relative z-10 flex max-h-[min(90dvh,680px)] w-full max-w-xl flex-col overflow-hidden rounded-xl border bg-background text-foreground shadow-2xl ${className}`}
        onKeyDown={(event) => {
          if (event.key !== "Tab") return
          const focusable = event.currentTarget.querySelectorAll<HTMLElement>('button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
          const first = focusable[0]
          const last = focusable[focusable.length - 1]
          if (event.shiftKey && document.activeElement === first) {
            event.preventDefault()
            last?.focus()
          } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault()
            first?.focus()
          }
        }}
      >
        <header className="flex shrink-0 items-start justify-between gap-3 border-b bg-muted/20 px-4 py-3 sm:px-6 sm:py-4">
          <div className="flex min-w-0 items-start gap-3">
            <span className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <Info className="size-4" aria-hidden="true" />
            </span>
            <div className="min-w-0">
              <h2 id={titleId} className="text-base font-semibold tracking-tight sm:text-lg">{title}</h2>
              {description && <p id={descriptionId} className="mt-0.5 text-xs text-muted-foreground">{description}</p>}
              {meta && <div className="mt-1 text-xs text-muted-foreground">{meta}</div>}
            </div>
          </div>
          <ReportModalCloseButton buttonRef={closeButtonRef} label="Tutup informasi" onClick={onClose} />
        </header>
        <div className="min-h-0 overflow-auto p-4 sm:p-6">{children}</div>
      </section>
    </ReportDetailLayer>
  )
}
