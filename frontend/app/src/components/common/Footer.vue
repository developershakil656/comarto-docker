<template>
  <footer class="bg-gradient-to-br from-gray-50 to-gray-100 border-t border-gray-200 pt-12 pb-6">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
        <!-- Company Info -->
        <div class="space-y-4">
          <h3 class="text-lg font-bold text-gray-800">Comarto</h3>
          <p class="text-gray-600 text-sm">
            Connecting businesses and customers through our comprehensive directory and marketplace platform.
          </p>
          <div class="flex space-x-4">
            <!-- Social Icons -->
            <a href="https://facebook.com/comartobd" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-primary transition-colors">
              <img src="@/assets/icons/svgs/FacebookIcon.svg" alt="Facebook" class="w-6 h-6" />
            </a>
            <a href="https://wa.me/8801569123838" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-primary transition-colors">
              <img src="@/assets/icons/svgs/WhatsappIcon.svg" alt="WhatsApp" class="w-6 h-6" />
            </a>
            <!-- <a href="https://twitter.com/comarto" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-primary transition-colors">
              <img src="@/assets/icons/svgs/XIcon.svg" alt="X (Twitter)" class="w-6 h-6" />
            </a>
            <a href="https://instagram.com/comarto" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-primary transition-colors">
              <img src="@/assets/icons/svgs/InstagramIcon.svg" alt="Instagram" class="w-6 h-6" />
            </a>
            <a href="https://linkedin.com/company/comarto" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-primary transition-colors">
              <img src="@/assets/icons/svgs/LinkedInIcon.svg" alt="LinkedIn" class="w-6 h-6" />
            </a> -->
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li><router-link to="/" class="text-gray-600 hover:text-primary transition-colors">Home</router-link></li>
            <li><router-link to="/categories" class="text-gray-600 hover:text-primary transition-colors">Categories</router-link></li>
            <li><router-link to="/search/all-bangladesh" class="text-gray-600 hover:text-primary transition-colors">Search</router-link></li>
            <li><router-link to="/free/listing" class="text-gray-600 hover:text-primary transition-colors">Add Business</router-link></li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h3 class="text-lg font-bold text-gray-800 mb-4">Legal</h3>
          <ul class="space-y-2">
            <li><router-link to="/privacy-policy" class="text-gray-600 hover:text-primary transition-colors">Privacy Policy</router-link></li>
            <li><router-link to="/terms-conditions" class="text-gray-600 hover:text-primary transition-colors">Terms & Conditions</router-link></li>
            <li><router-link to="/disclaimer" class="text-gray-600 hover:text-primary transition-colors">Disclaimer</router-link></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-lg font-bold text-gray-800 mb-4">Contact Us</h3>
          <ul class="space-y-2 text-gray-600">
            <li class="flex items-start">
              <EnvelopeIcon class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" />
              <span>comarto.com@gmail.com</span>
            </li>
            <li class="flex items-start">
              <PhoneIcon class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" />
              <span>+8801569123838</span>
            </li>
            <li class="flex items-start">
              <MapPinIcon class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" />
              <span>Dhaka, Bangladesh</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="border-t border-gray-200 pt-6 mt-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-600 text-sm mb-4 md:mb-0">
            &copy; {{ currentYear }} Comarto. All rights reserved.
          </p>
          <div class="flex space-x-6">
            <router-link to="/privacy-policy" class="text-gray-600 hover:text-primary text-sm transition-colors">Privacy Policy</router-link>
            <router-link to="/terms-conditions" class="text-gray-600 hover:text-primary text-sm transition-colors">Terms & Conditions</router-link>
            <router-link to="/disclaimer" class="text-gray-600 hover:text-primary text-sm transition-colors">Disclaimer</router-link>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { EnvelopeIcon, PhoneIcon, MapPinIcon } from '@heroicons/vue/24/outline';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
  name: 'Footer',
  components: {
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    OptimizedImage
  },
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>