<template>
  <section class="py-20 bg-gradient-to-br from-primary-50 via-primary-100 to-primary-50 relative overflow-hidden">
    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 rounded-full opacity-20 animate-pulse"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary-100 rounded-full opacity-20 animate-pulse" style="animation-delay: 1s;"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary-100 rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
      <div class="max-w-5xl mx-auto">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-4 md:p-16 border border-white/20">
          <!-- Header Section -->
          <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full mb-6 shadow-lg">
              <BuildingOfficeIcon class="w-10 h-10 text-white" />
            </div>
            
            <h2 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-gray-800 via-primary-800 to-primary-900 bg-clip-text text-transparent mb-6 leading-tight">
              Are you a Supplier or Manufacturer?
            </h2>
            
            <p class="text-xl md:text-3xl font-semibold text-gray-700 mb-4">
              List your Business Today!
            </p>
            
            <p class="text-base md:text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
              Join thousands of businesses already listed on our platform. Get discovered by potential customers, showcase your products, and grow your business with our comprehensive B2B directory.
            </p>
          </div>

          <!-- Benefits Grid -->
          <div class="grid md:grid-cols-3 gap-6 mb-12">
            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-pink-50 to-red-50 border border-pink-100">
              <div class="w-16 h-16 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <CurrencyDollarIcon class="w-8 h-8 text-white" />
              </div>
              <h3 class="text-xl font-bold text-gray-800 mb-2">Free to List</h3>
              <p class="text-gray-600">No hidden fees or subscription costs</p>
            </div>

            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100">
              <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <BoltIcon class="w-8 h-8 text-white" />
              </div>
              <h3 class="text-xl font-bold text-gray-800 mb-2">Quick Setup</h3>
              <p class="text-gray-600">Get your business listed in minutes</p>
            </div>

            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100">
              <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <UserGroupIcon class="w-8 h-8 text-white" />
              </div>
              <h3 class="text-xl font-bold text-gray-800 mb-2">Start Getting Leads</h3>
              <p class="text-gray-600">Connect with potential customers instantly</p>
            </div>
          </div>

          <!-- CTA Section -->
          <div class="text-center">
            <button 
              @click="handleRegisterClick"
              class="group max-w-full relative inline-flex items-center justify-center px-4 md:px-12 py-6 text-base md:text-xl font-bold text-white bg-gradient-to-r from-primary-600 via-primary-600 to-primary-dark rounded-2xl shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 overflow-hidden"
            >
              <!-- Button background animation -->
              <div class="absolute inset-0 bg-gradient-to-r from-primary-700 via-primary-700 to-primary-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              
              <!-- Button content -->
              <span class="relative z-10 flex items-center">
                <PlusIcon class="w-6 h-6 mr-3" />
                Register Your Business
                <ArrowRightIcon class="w-5 h-5 ml-3 transform group-hover:translate-x-1 transition-transform duration-300" />
              </span>
            </button>

            <!-- Additional info -->
            <div class="mt-8 flex flex-col md:flex-row items-center justify-center gap-6 text-gray-600">
              <div class="flex items-center">
                <CheckCircleIcon class="w-5 h-5 mr-2 text-primary-500" />
                <span class="font-medium">100% Free Registration</span>
              </div>
              <div class="flex items-center">
                <CheckCircleIcon class="w-5 h-5 mr-2 text-primary-500" />
                <span class="font-medium">No Credit Card Required</span>
              </div>
              <div class="flex items-center">
                <CheckCircleIcon class="w-5 h-5 mr-2 text-primary-500" />
                <span class="font-medium">Instant Approval</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { 
  BuildingOfficeIcon,
  CurrencyDollarIcon,
  BoltIcon,
  UserGroupIcon,
  PlusIcon,
  ArrowRightIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

export default {
  name: 'BusinessRegistrationCTA',
  components: {
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    BoltIcon,
    UserGroupIcon,
    PlusIcon,
    ArrowRightIcon,
    CheckCircleIcon
  },
  props: {
    show: {
      type: Boolean,
      default: true
    }
  },
  emits: ['register-click'],
  methods: {
    handleRegisterClick() {
      this.$emit('register-click');
    }
  }
}
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Custom shadow for better depth */
.shadow-3xl {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}
</style>
