import { useEffect, useRef, useState } from "react"
import { createPortal } from "react-dom"
import { AlertCircle, CheckCircle2, Info, X } from "lucide-react"

import { Button } from "@/components/ui/button"

type ToastVariant = "success" | "error" | "info"

const toastStyles: Record<ToastVariant, { Icon: typeof CheckCircle2; className: string }> = {
  success: { Icon: CheckCircle2, className: "border-signal-green/40 bg-signal-green/10 text-signal-green-foreground" },
  error: { Icon: AlertCircle, className: "border-destructive/40 bg-destructive/10 text-destructive" },
  info: { Icon: Info, className: "border-border text-foreground" },
}

export function AppToast({
  open,
  message,
  variant = "success",
  duration = 4200,
  onDismiss,
}: {
  open: boolean
  message: string
  variant?: ToastVariant
  duration?: number
  onDismiss: () => void
}) {
  const [top, setTop] = useState(72)
  const dismissRef = useRef(onDismiss)
  useEffect(() => { dismissRef.current = onDismiss }, [onDismiss])

  useEffect(() => {
    if (!open || duration <= 0) return undefined
    const timeout = window.setTimeout(() => dismissRef.current(), duration)
    return () => window.clearTimeout(timeout)
  }, [duration, message, open])

  useEffect(() => {
    if (!open) return undefined
    const navbar = document.getElementById("monbis-app-navbar")
    const updatePosition = () => setTop(navbar ? navbar.getBoundingClientRect().bottom + 8 : 72)
    updatePosition()
    const observer = navbar ? new ResizeObserver(updatePosition) : null
    if (navbar && observer) observer.observe(navbar)
    window.addEventListener("resize", updatePosition)
    return () => {
      observer?.disconnect()
      window.removeEventListener("resize", updatePosition)
    }
  }, [open])

  if (!open || !message) return null
  const { Icon, className } = toastStyles[variant]

  return createPortal(
    <div
      role={variant === "error" ? "alert" : "status"}
      aria-live={variant === "error" ? "assertive" : "polite"}
      className={`fixed right-3 z-[80] flex w-[min(22rem,calc(100vw-1.5rem))] items-center gap-2 rounded-lg border bg-background px-3 py-2 text-xs shadow-md sm:right-5 ${className}`}
      style={{ top }}
    >
      <Icon className="size-4 shrink-0" aria-hidden="true" />
      <p className="min-w-0 flex-1 line-clamp-2" title={message}>{message}</p>
      <Button type="button" variant="ghost" size="icon-xs" className="size-6" aria-label="Tutup notifikasi" onClick={onDismiss}>
        <X className="size-3.5" aria-hidden="true" />
      </Button>
    </div>,
    document.body,
  )
}
