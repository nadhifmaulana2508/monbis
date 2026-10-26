import { createPortal } from "react-dom"
import type { ReactNode } from "react"

export function ReportDetailLayer({ children, className = "" }: { children: ReactNode; className?: string }) {
  return createPortal(
    <div className={`fixed inset-0 z-[2147483000] ${className}`} role="presentation">
      {children}
    </div>,
    document.body,
  )
}
