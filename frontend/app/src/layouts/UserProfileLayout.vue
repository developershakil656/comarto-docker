<template>

    <div class="bg-gray-50 md:py-6 lg:py-8 md:px-4 lg:px-6">
      <div class="flex flex-col md:flex-row min-h-screen gap-4 lg:gap-6">
        <!-- Sidebar -->
        <aside class="lg:w-80">
          <ProfileSideBar />
        </aside>

        <div class="flex-1 flex flex-col items-center">
          <div
            class="shadow-lg md:rounded-lg p-4 lg:p-8 w-full max-w-7xl flex flex-col min-h-screen bg-gradient-to-br from-primary/5 to-primary/10"
          >
            <router-view />
          </div>
        </div>
      </div>
    </div>

</template>

<script>
import ProfileSideBar from "@/components/user/ProfileSideBar.vue";
export default {
  components: {
    ProfileSideBar,
  },
  data() {
    return {
      isDesktop: false,
    };
  },
  mounted() {
    this.checkScreenSize();
    window.addEventListener("resize", this.checkScreenSize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkScreenSize);
  },
  methods: {
    checkScreenSize() {
      // Tailwind's 'md' breakpoint is 768px
      this.isDesktop = window.innerWidth >= 768;
      this.$emit("show-header", this.isDesktop);
    },
  },
  // Add this navigation guard
  beforeRouteLeave(to, from, next) {
    // When leaving the UserProfile section, tell the parent to show the header again
    this.$emit("show-header", true);
    next();
  },
};
</script>
