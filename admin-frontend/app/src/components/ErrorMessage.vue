<template>
  <transition name="fade">
    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>
  </transition>
</template>

<script setup>
defineProps({
  error: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
