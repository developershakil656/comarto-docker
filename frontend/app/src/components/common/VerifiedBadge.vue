<template>
  <div v-if="accountVerified" class="inline-flex items-center gap-1 justify-center">
    <div class=" text-blue-600 font-bold bg-blue-100 px-2 py-0.5 rounded-md flex items-center gap-1">
    <IdentificationIcon class="w-4 md:w-5" />
    <span class="text-xs">Verified</span>
  </div>
  </div>
</template>

<script>
import { IdentificationIcon } from '@heroicons/vue/24/outline';
export default {
  name: 'VerifiedBadge',
  components: {
    IdentificationIcon
  },
  props: {
    accountVerified: {
      type: Boolean,
      default: false
    }
  },
  // computed: {
  //   isVerified() {
  //     if (this.accountVerified) {
  //       return this.accountVerified;
  //     }
  //     return false;
  //   }
  // }
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
