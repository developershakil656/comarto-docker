<template>
  <div v-if="isVisible" class="fixed inset-0 z-[60] flex justify-end bg-black bg-opacity-40" @click="closeSidebar">
    <div class="w-72 sm:w-80 bg-white h-full shadow-lg p-5 relative flex flex-col overflow-y-auto" :class="isClosing ? 'animate-slide-out-right' : 'animate-slide-in-right'" @click.stop>
      <button @click="closeSidebar" class="absolute top-3 text-gray-600 hover:text-gray-800 bg-gray-200 p-2 rounded"><ChevronLeftIcon class="h-5"/></button>
      <div class="flex flex-col items-center mt-8 pb-4 border-b">
        <img :src="user?.profile || 'https://placehold.co/150x150/0b845c/white?text=Profile'" class="w-28 h-28 rounded-full object-cover border-2 border-gray-300" alt="Profile" />
        <div class="mt-2 font-semibold text-base md:text-lg">{{ user?.name || 'User' }}</div>
        <div class="text-xs sm:text-sm line-clamp-1 text-gray-500">{{ user?.email || user?.number || '' }}</div>
      </div>
      <div class="">
        <ul class="space-y-2 border-b mt-2 pb-2">
          <li>
            <router-link to="/my/business/dashboard" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            @click="closeSidebar"
            >
              <BuildingOfficeIcon class="mr-3 w-5 h-5" />
              <span>Switch to Business</span>
            </router-link>
          </li>
          
          <li>
            <router-link to="/user/account/details" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            :class="$route.path === '/user/account/details' ? 'text-primary bg-primary/10' : ''"
            @click="closeSidebar"
            >
              <UserIcon class="mr-3 w-5 h-5" />
              <span>Profile</span>
            </router-link>
          </li>

          <li>
            <router-link to="/user/favourites" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            :class="$route.path === '/user/favourites' ? 'text-primary bg-primary/10' : ''"
            @click="closeSidebar"
            >
              <HeartIcon class="mr-3 w-5 h-5" />
              <span>Favorites</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/following" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            :class="$route.path === '/user/following' ? 'text-primary bg-primary/10' : ''"
            @click="closeSidebar"
            >
              <img src="@/assets/icons/svgs/FollowStoreIcon.svg" alt="Follow Store" class="w-4 h-4 mr-3 inline" :class="{
                            'filter-primary': $route.path === '/user/following',
                        }"/>
              <span>Following</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/reviews" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            :class="$route.path === '/user/reviews' ? 'text-primary bg-primary/10' : ''"
            @click="closeSidebar"
            >
              <StarIcon class="mr-3 w-5 h-5" />
              <span>My Reviews</span>
            </router-link>
          </li>
        </ul>
      </div>
      <div class="mt-6 space-y-2">
        <!-- <div class="flex items-center justify-between">
          <span>Change language</span>
          <select class="border rounded px-2 py-1 text-sm">
            <option>English</option>
            <option>Hindi</option>
          </select>
        </div> -->
        <ul class="mt-2 space-y-2">
          <!-- <li>
            <button class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left">
              <BellAlertIcon class="mr-3 w-5 h-5" />
              <span>Notifications</span>
            </button>
          </li> -->
          <li>
            <router-link to="/user/inquiries" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            :class="$route.path === '/user/inquiries' ? 'text-primary bg-primary/10' : ''"
            @click="closeSidebar">
              <ChatBubbleOvalLeftEllipsisIcon class="mr-3 w-5 h-5" /><span>Inquiries</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/identity/verify" class="w-full flex items-center py-2 px-2 rounded hover:bg-primary/10 text-left"
            :class="$route.path === '/user/identity/verify' ? 'text-primary bg-primary/10' : ''"
            @click="closeSidebar">
              <IdentificationIcon class="mr-3 w-5 h-5" /><span>Identity Verify</span>
            </router-link>
          </li>
          <li>
            <button @click="handleLogout" class="w-full flex items-center py-2 px-2 rounded hover:bg-red-50 text-left text-red-600">
              <ArrowRightOnRectangleIcon class="mr-3 w-5 h-5" />
              <span>Logout</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { HeartIcon, ChevronLeftIcon, BookmarkIcon, UserIcon, Cog6ToothIcon, BellAlertIcon, ChatBubbleOvalLeftEllipsisIcon, PhoneIcon, BuildingOffice2Icon, ArrowRightOnRectangleIcon, BuildingOfficeIcon, IdentificationIcon, StarIcon  } from '@heroicons/vue/24/outline';
import { useModalScroll } from '@/composables/useModalScroll';
export default {
  name: 'UserSlider',
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  components: {
    ChevronLeftIcon,
    HeartIcon,
    BookmarkIcon,
    UserIcon,
    Cog6ToothIcon,
    BellAlertIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    PhoneIcon,
    BuildingOffice2Icon,
    ArrowRightOnRectangleIcon,
    BuildingOfficeIcon,
    IdentificationIcon,
    StarIcon
  },
  data() {
    return {
      isVisible: this.show,
      isClosing: false,
    }
  },
  computed: {
    user() {
      return this.$store.getters.user;
    },
  },
  setup() {
    const { openModal, closeModal } = useModalScroll();
    return { openModal, closeModal };
  },
  methods: {
    handleLogout() {
      this.isClosing = true; // Start animation before logging out
      this.$store.dispatch('logout');
      this.closeSidebar();
      // Redirect to home page
      this.$router.push('/');
    },
    closeSidebar() {
      this.isClosing = true;
      this.closeModal('user-slider');
      // Wait for animation to finish before hiding
      setTimeout(() => {
        this.isVisible = false;
        this.$emit('close');
      }, 400); // Match animation duration
    },
  },
  watch: {
    show(newVal) {
      this.isVisible = newVal;
      if (newVal) {
        this.isClosing = false;
        // Lock body scroll when slider opens
        this.openModal('user-slider');
        console.log(newVal + "==" + 'show')
      } else {
        // Unlock body scroll when slider closes
        this.closeModal('user-slider');
        console.log(newVal + "==" + 'unlock')
      }
    }
  },
  beforeUnmount() {
    // Ensure body scroll is unlocked when component is destroyed
    if (this.show) {
      this.closeModal('user-slider');
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