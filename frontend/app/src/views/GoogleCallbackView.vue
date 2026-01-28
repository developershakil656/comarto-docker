<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-green-50">
    <div class="max-w-md w-full mx-4">
      <!-- Main Card -->
      <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back!</h1>
          <p class="text-gray-600">Completing your sign-in process</p>
        </div>

        <!-- Loading Animation -->
        <div class="text-center mb-8">
          <!-- Spinning Circle -->
          <div class="relative mx-auto w-20 h-20 mb-6">
            <div class="absolute inset-0 rounded-full border-4 border-gray-200"></div>
            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-500 animate-spin"></div>
            <div class="absolute inset-2 rounded-full bg-blue-50 flex items-center justify-center">
              <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <!-- Progress Steps -->
          <div class="space-y-3">
            <div class="flex items-center justify-center space-x-3">
              <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              <span class="text-sm text-gray-600">Connecting to Google</span>
            </div>
            <div class="flex items-center justify-center space-x-3">
              <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
              <span class="text-sm text-gray-600">Verifying credentials</span>
            </div>
            <div class="flex items-center justify-center space-x-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              <span class="text-sm text-gray-400">Setting up your account</span>
            </div>
          </div>
        </div>

        <!-- Status Message -->
        <div class="text-center">
          <p class="text-sm text-gray-500 mb-4">
            {{ statusMessage }}
          </p>
          
          <!-- Progress Bar -->
          <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div 
              class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-1000 ease-out"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
          
          <p class="text-xs text-gray-400">
            This may take a few moments...
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center mt-6">
        <p class="text-xs text-gray-400">
          Secure authentication powered by Google
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GoogleCallbackView',
  data() {
    return {
      code: null,
      error: null,
      statusMessage: 'Initializing authentication...',
      progress: 0,
      progressInterval: null
    }
  },
  async mounted() {
    // Start progress animation
    this.startProgressAnimation();
    
    try {
      const urlParams = new URLSearchParams(window.location.search);
      this.code = urlParams.get('code');
      this.error = urlParams.get('error');

      if (this.error) {
        this.statusMessage = 'Authentication failed. Redirecting...';
        this.progress = 100;
        setTimeout(() => {
          this.$router.replace('/?error=auth_failed');
        }, 1500);
        return;
      }

      if (!this.code) {
        this.statusMessage = 'No authorization code received. Redirecting...';
        this.progress = 100;
        setTimeout(() => {
          this.$router.replace('/?error=no_code');
        }, 1500);
        return;
      }

      // Extract state parameter to determine if this is for email update
      const state = urlParams.get('state');
      
      this.statusMessage = 'Verifying your credentials...';
      this.progress = 50;
      
      await this.$store.dispatch('handleGoogleCallback', { code: this.code, state });
      
      this.statusMessage = 'Success! Setting up your account...';
      this.progress = 90;
      
      setTimeout(() => {
        this.progress = 100;
        this.statusMessage = 'Welcome! Redirecting...';
        setTimeout(() => {
          // Check if this was for email update
          if (state === 'email_update') {
            // Get the stored redirect route for email update
            const redirectRoute = localStorage.getItem('googleEmailUpdateRedirect') || '/';
            localStorage.removeItem('googleEmailUpdateRedirect'); // Clean up
            this.$router.replace(redirectRoute);
          } else {
            // Get the stored redirect route for regular login, or default to home
            const redirectRoute = localStorage.getItem('googleLoginRedirect') || '/';
            localStorage.removeItem('googleLoginRedirect'); // Clean up
            
            // Redirect to the stored route or home
            this.$router.replace(redirectRoute);
          }
        }, 1000);
      }, 1000);
      
    } catch (error) {
      this.statusMessage = 'Something went wrong. Redirecting...';
      this.progress = 100;
      setTimeout(() => {
        this.$router.replace('/?error=callback_failed');
      }, 1500);
    }
  },
  methods: {
    startProgressAnimation() {
      this.progressInterval = setInterval(() => {
        if (this.progress < 30) {
          this.progress += Math.random() * 5;
        }
      }, 200);
    }
  },
  beforeUnmount() {
    if (this.progressInterval) {
      clearInterval(this.progressInterval);
    }
  }
}
</script>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style> 