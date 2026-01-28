<template>
  <div v-if="isVisible" class="fixed inset-0 z-[60] flex justify-end bg-black bg-opacity-40 capitalize" @click="closeSidebar">
    <div class="w-72 sm:w-80 bg-white h-full shadow-lg p-5 relative flex flex-col overflow-y-auto" :class="isClosing ? 'animate-slide-out-right' : 'animate-slide-in-right'" @click.stop>
      <button @click="closeSidebar" class="absolute top-3 left-3 text-gray-600 hover:text-gray-800 bg-gray-200 p-2 rounded transition-colors">
        <ChevronLeftIcon class="w-5 h-5" />
      </button>
      <div class="flex flex-col items-center mt-8 pb-4 border-b">
        <img :src="myBusiness?.business_profile || 'https://placehold.co/150x150/0b845c/white?text=Profile'" class="w-28 h-28 rounded-full object-cover border-2 border-gray-300" alt="Profile" />
        <div class="mt-2 font-semibold text-base md:text-lg">{{ myBusiness.business_name }}</div>
        <div class="text-xs sm:text-sm line-clamp-1 text-gray-500">{{ myBusiness.name }}</div>
      </div>
      <button class="w-full px-4 py-2 my-4 bg-primary text-white font-semibold rounded-lg shadow hover:bg-primary/85 transition flex items-center justify-center gap-2">
        <MegaphoneIcon class="w-5 h-5" />Advertise
      </button>
      <nav class="w-full mb-6">
        <ul class="space-y-2 w-full">
          <li>
            <router-link
              to="/my/business/dashboard"
              class="flex items-center w-full px-4 py-2 rounded-md hover:bg-primary/10 transition font-medium"
              :class="$route.path === '/my/business/dashboard' ? 'text-primary bg-primary/10' : ''"
            >
              <Squares2X2Icon class="w-5 h-5 mr-3" />Dashboard
            </router-link>
          </li>
          <li>
            <router-link to="/" class="flex items-center w-full px-4 py-2 rounded-md hover:bg-gray-200 transition font-medium">
              <HomeIcon class="w-5 h-5 mr-3" />Switch to Home
            </router-link>
          </li>
          <li>
            <router-link
              to="/my/business/details"
              class="flex items-center w-full px-4 py-2 rounded-md hover:bg-primary/10 transition font-medium"
              :class="$route.path === '/my/business/details' ? 'text-primary bg-primary/10' : ''"
            >
              <PencilSquareIcon class="w-5 h-5 mr-3" />Business Details
            </router-link>
          </li>
          <li>
            <router-link
              to="/my/business/products"
              class="flex items-center w-full px-4 py-2 rounded-md hover:bg-primary/10 transition font-medium"
              :class="$route.path === '/my/business/products' ? 'text-primary bg-primary/10' : ''"
            >
              <TagIcon class="w-5 h-5 mr-3" />My Products
            </router-link>
          </li>
          <li>
            <router-link
              to="/my/business/reviews"
              class="flex items-center w-full px-4 py-2 rounded-md hover:bg-primary/10 transition font-medium"
              :class="$route.path === '/my/business/reviews' ? 'text-primary bg-primary/10' : ''"
            >
              <StarIcon class="w-5 h-5 mr-3" />Reviews
            </router-link>
          </li>
          <li>
            <router-link
              to="/my/business/leads"
              class="flex items-center w-full px-4 py-2 rounded-md hover:bg-primary/10 transition font-medium"
              :class="$route.path === '/my/business/leads' ? 'text-primary bg-primary/10' : ''"
            >
              <InboxIcon class="w-5 h-5 mr-3" />Leads
            </router-link>
          </li>
          <li>
            <router-link
              to="/my/business/lead-credits/buy"
              class="flex items-center w-full px-4 py-2 rounded-md hover:bg-primary/10 transition font-medium"
              :class="$route.path === '/my/business/lead-credits/buy' ? 'text-primary bg-primary/10' : ''"
            >
              <BanknotesIcon class="w-5 h-5 mr-3" />Buy Lead Credits
            </router-link>
          </li>
        </ul>
      </nav>
      <button class="w-full px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition flex items-center justify-center gap-2 mt-auto" @click="logout">
        <ArrowLeftOnRectangleIcon class="w-5 h-5" />Logout
      </button>
    </div>
  </div>
</template>

<script>
import {
  Squares2X2Icon,
  PencilSquareIcon,
  TagIcon,
  StarIcon,
  MegaphoneIcon,
  ChevronLeftIcon,
  HomeIcon,
  ArrowLeftOnRectangleIcon,
  InboxIcon,
  BanknotesIcon
} from '@heroicons/vue/24/outline';

export default {
  name: 'BusinessSlider',
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  components: {
    Squares2X2Icon,
    PencilSquareIcon,
    TagIcon,
    StarIcon,
    MegaphoneIcon,
    ChevronLeftIcon,
    HomeIcon,
    ArrowLeftOnRectangleIcon,
    InboxIcon,
    BanknotesIcon
  },
  data() {
    return {
      isVisible: this.show,
      isClosing: false
    }
  },
  computed: {
    myBusiness() {
      return this.$store.getters.myBusiness;
    }
  },
  methods: {
    logout() {
      this.isClosing = true; // Start animation before logging out
      this.$store.dispatch('logout');
      this.closeSidebar();
      // Redirect to home page
      this.$router.push('/');
    },
    closeSidebar() {
      if (this.isClosing) return;
      this.isClosing = true;
      setTimeout(() => {
        this.isVisible = false;
        this.$emit('close');
      }, 400); // Match animation duration
    }
  },
  watch: {
    show(newVal) {
      this.isVisible = newVal;
      if (newVal) {
        this.isClosing = false;
      }
    }
  }
}
</script>

<style scoped>
@keyframes slide-in-right {
  0% { transform: translateX(100%); }
  100% { transform: translateX(0); }
}

@keyframes slide-out-right {
  0% { transform: translateX(0); }
  100% { transform: translateX(100%); }
}

.animate-slide-in-right {
  animation: slide-in-right 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-slide-out-right {
  animation: slide-out-right 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style> 