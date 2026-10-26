import type { Ref } from "react"
import { X } from "lucide-react"

import { Button } from "@/components/ui/button"

type Props = {
  onClick: () => void
  label: string
  buttonRef?: Ref<HTMLButtonElement>
}

export function ReportModalCloseButton({ onClick, label, buttonRef }: Props) {
  return (
    <Button ref={buttonRef} type="button" variant="signalRed" size="icon-lg" className="size-9 shrink-0" aria-label={label} title={label} onClick={onClick}>
      <X className="size-4" aria-hidden="true" />
    </Button>
  )
}
