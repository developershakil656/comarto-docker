<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-600 px-4">
    
    <div v-if="isCheckingStatus" class="text-white text-center animate-pulse">
      <div class="mb-4 flex justify-center">
        <div class="h-12 w-12 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
      </div>
      <p class="text-lg font-medium">Checking app status...</p>
    </div>

    <div v-else-if="isAppInstalled" class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-2xl p-8 text-center animate-fadeIn">
        <div class="mb-6 flex justify-center">
          <img src="/logo.svg" alt="Comarto" class="h-16 w-auto" />
        </div>
        <div class="mb-4 flex justify-center text-green-500">
          <CheckCircleIcon class="w-16 h-16" />
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">App Ready</h2>
        <p class="text-gray-600 mb-6">Comarto is already installed on your device.</p>
        <button @click="launchApp" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-3 rounded-lg">
          Open App
        </button>
      </div>
    </div>

    <div v-else class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-2xl p-8 animate-fadeIn">
        <div class="mb-6 flex justify-center">
          <img src="/logo.svg" alt="Comarto" class="h-16 w-auto" />
        </div>
        
        <div v-if="installPrompt">
          <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Install Comarto</h2>
          <p class="text-gray-600 text-center mb-6">Experience Comarto as a lightning-fast app.</p>
          <button @click="handleInstall" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-3 flex items-center justify-center gap-2 rounded-lg shadow-lg">
            <ArrowDownTrayIcon class="w-5 h-5" /> Install Now
          </button>
        </div>

        <div v-else-if="isIOS">
          <h2 class="text-xl font-bold mb-4 text-indigo-600">Install on iPhone:</h2>
          <ol class="space-y-4 text-gray-700 mb-6">
            <li class="flex gap-3">
              <span class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1</span>
              <span>Tap the <strong>Share</strong> button in Safari</span>
            </li>
            <li class="flex gap-3">
              <span class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>
              <span>Select <strong>Add to Home Screen</strong></span>
            </li>
          </ol>
        </div>

        <div v-else-if="isAndroid">
          <h2 class="text-xl font-bold mb-4 text-indigo-600 text-center">Still not installed?</h2>
          <p class="text-sm text-gray-500 mb-4 text-center">If an install button didn't appear, follow these steps:</p>
          <ol class="space-y-3 text-gray-700 mb-6">
            <li class="flex gap-3">
              <span class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1</span>
              <span>Tap the <strong>three dots</strong> menu</span>
            </li>
            <li class="flex gap-3">
              <span class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>
              <span>Tap <strong>Install app</strong> or <strong>Add to Home screen</strong></span>
            </li>
          </ol>
        </div>

        <button @click="continueInBrowser" class="w-full bg-gray-100 text-gray-600 font-semibold py-2 rounded-lg mt-2">
          Continue in Browser
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { CheckCircleIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const installPrompt = ref(null)
const isAppInstalled = ref(false)
const isCheckingStatus = ref(true) 
const isIOS = ref(false)
const isAndroid = ref(false)

onMounted(() => {
  const ua = navigator.userAgent.toLowerCase()
  isIOS.value = /iphone|ipad|ipod/.test(ua)
  isAndroid.value = /android/.test(ua)

  // Check if currently running in standalone mode
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                       window.navigator.standalone === true;
  
  if (isStandalone) {
    isAppInstalled.value = true
    isCheckingStatus.value = false
    return
  }

  // Listen for the native install prompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    installPrompt.value = e
    isCheckingStatus.value = false
  })

  // NEW: Listen for successful installation and redirect home
  window.addEventListener('appinstalled', () => {
    isAppInstalled.value = true
    localStorage.setItem('pwa_installed', 'true')
    // Redirect to home immediately after installation is complete
    router.push({ name: 'home' })
  })

  // Timeout to show manual instructions if no native prompt is detected
  setTimeout(() => {
    if (isCheckingStatus.value) {
      isCheckingStatus.value = false
    }
  }, 1500)
})

async function handleInstall() {
  if (!installPrompt.value) return
  installPrompt.value.prompt()
  const { outcome } = await installPrompt.value.userChoice
  if (outcome === 'accepted') {
    installPrompt.value = null
  }
}

function launchApp() { window.location.href = '/' }
function continueInBrowser() { router.push({ name: 'home' }) }
</script>

<style scoped>
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.4s ease-out; }
</style>