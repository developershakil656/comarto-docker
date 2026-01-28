<template>
  <div class="border border-gray-300 p-4 rounded-md mb-4">
    <div class="flex items-center mb-2 md:mb-4">
      <div class="bg-primary text-white rounded-md text-lg md:text-3xl font-bold mr-2 md:mr-4">
        <p class="px-2 py-3">{{ rating.rate || 0 }}<span class="text-lg">/5</span></p>
      </div>
      <div>
        <div>
          <StarRating :rating="rating.rate" iconSize="h-8 md:h-10" />
        </div>
        <div class="text-base pl-1">{{ rating.count }} Ratings</div>
      </div>
    </div>
  </div>
</template>

<script>
import StarRating from '@/components/StarRating.vue'

export default {
  name: 'ReviewRating',
  components: {
    StarRating
  },
  props: {
    rating: {
      type: Object,
      default: () => ({ rate: 0, count: 0 })
    }
  }
}
</script> 