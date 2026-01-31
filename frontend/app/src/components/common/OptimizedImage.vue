<template>
  <img 
    :src="currentSrc" 
    :alt="alt"
    :loading="loading"
    :class="computedClasses"
    @load="onLoad"
    @error="onError"
    ref="imageRef"
  >
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  lazy: {
    type: Boolean,
    default: true
  },
  width: {
    type: [String, Number],
    default: null
  },
  height: {
    type: [String, Number],
    default: null
  },
  class: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['load', 'error'])

const imageRef = ref(null)
const isLoaded = ref(false)
const hasError = ref(false)
const currentSrc = ref(props.src)

const loading = computed(() => props.lazy ? 'lazy' : 'eager')

const computedClasses = computed(() => {
  const classes = [props.class]
  if (!isLoaded.value && !hasError.value) {
    classes.push('opacity-0')
  }
  return classes.join(' ')
})

const onLoad = () => {
  isLoaded.value = true
  hasError.value = false
  emit('load')
}

const onError = () => {
  hasError.value = true
  // Set to fallback image
  currentSrc.value = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIE5vdCBGb3VuZDwvdGV4dD48L3N2Zz4='
  emit('error')
}

onMounted(() => {
  // Check if image is already loaded
  if (imageRef.value?.complete && imageRef.value?.naturalWidth !== 0) {
    isLoaded.value = true
  }
})

// Watch for prop changes
watch(() => props.src, (newSrc) => {
  if (newSrc) {
    currentSrc.value = newSrc
    isLoaded.value = false
    hasError.value = false
  }
}, { immediate: true })
</script>

<style scoped>
img {
  transition: opacity 0.3s ease;
}
.opacity-0 {
  opacity: 0;
}
</style>