import { forwardRef, type SelectHTMLAttributes } from "react"

import { cn } from "@/lib/utils"

export const Select = forwardRef<HTMLSelectElement, SelectHTMLAttributes<HTMLSelectElement>>(({ className, ...props }, ref) => (
  <select ref={ref} className={cn("h-9 w-full rounded-md border border-[var(--line)] bg-[var(--surface)] px-3 text-sm text-[var(--ink)] outline-none transition-colors focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]", className)} {...props} />
))
Select.displayName = "Select"
