import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss()],
  server: {
    port: 5173,
    host: true,
    allowedHosts: [
      process.env.VITE_ALLOWED_HOST || 'admin.mydomain.com',  // Add your custom domain here
      '0.0.0.0',        // Allow all IPs, use cautiously in production environments
    ],
  },
})
