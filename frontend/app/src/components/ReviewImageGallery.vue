<template>
  <div v-if="images && images.length > 0" class="mt-3">
    <ImageSlider :images="images" sliderTitle="Review Images" :visibleCount="4" />
  </div>
</template>

<script>
import ImageSlider from '@/components/common/ImageSlider.vue'

export default {
  name: 'ReviewImageGallery',
  components: {
    ImageSlider
  },
  props: {
    images: {
      type: Array,
      required: true
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar for thumbnails */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style> 