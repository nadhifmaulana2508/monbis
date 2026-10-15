import path from "node:path"

import react from "@vitejs/plugin-react"
import tailwindcss from "@tailwindcss/vite"
import { defineConfig } from "vite"

export default defineConfig(({ mode }) => ({
  base: mode === "production" ? "/kpi/" : "/",
  plugins: [react(), tailwindcss()],
  server: {
    port: 5173,
    proxy: {
      "/api": {
        target: "http://localhost",
        changeOrigin: true,
        rewrite: (requestPath) => requestPath.replace(/^\/api/, "/report-dpk/api"),
      },
    },
    fs: {
      allow: [process.cwd(), path.resolve(process.cwd(), "../frontend/node_modules")],
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(process.cwd(), "src"),
    },
  },
}))
