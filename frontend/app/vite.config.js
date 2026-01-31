import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src')
    }
  },
  server: {
    port: 5173,
    host: true,
    allowedHosts: [
      process.env.VITE_ALLOWED_HOST || 'mydomain.com',
      '0.0.0.0',
    ],
  },
  css: {
    postcss: './postcss.config.js'
  },
  // Production optimizations
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          // Split vendor chunks
          'vendor-vue': ['vue', 'vue-router'],
          'vendor-forms': ['vee-validate', 'yup', '@vee-validate/rules'],
          'vendor-ui': ['@heroicons/vue', 'sweetalert2', 'notivue'],
          'vendor-utils': ['axios']
        }
      }
    },
    // Enable CSS code splitting
    cssCodeSplit: true,
    // Minify CSS and JS
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true
      }
    },
    // Generate separate CSS file
    cssMinify: true
  },
  // Preload important resources
  optimizeDeps: {
    include: ['vue', 'vue-router']
  }
})
