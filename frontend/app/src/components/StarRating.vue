<template>
  <div class="flex items-center">
    <div v-for="i in 5" :key="i">
      <div 
        @click="editable ? handleStarClick(i) : null"
        @mouseenter="editable ? handleMouseEnter(i) : null"
        @mouseleave="editable ? handleMouseLeave() : null"
        :class="{ 'cursor-pointer': editable, 'cursor-default': !editable }"
      >
        <StarIcon 
          class="border rounded-md transition-colors duration-200" 
          :class="[getStarClass(i), iconSize, iconMargin, iconPadding]"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { StarIcon } from '@heroicons/vue/24/outline'
import { computed, ref } from 'vue'

const props = defineProps({
  rating: {
    type: [Number, String],
    required: true
  },
  iconSize: {
    type: String,
    default: 'h-4'
  },
  editable: {
    type: Boolean,
    default: false
  },
  iconMargin:{
    type: String,
    default: 'mr-0.5'
  },
  iconPadding:{
    type: String,
    default: 'p-0.5'
  }
})

const emit = defineEmits(['rating-changed'])

// Hover state for interactive stars
const hoverRating = ref(0)

// Convert rating to number to handle both string and number inputs
const ratingValue = computed(() => {
  return typeof props.rating === 'string' ? parseFloat(props.rating) : props.rating
})

// Get the effective rating (either hover or actual rating)
const effectiveRating = computed(() => {
  if (props.editable && hoverRating.value > 0) {
    return hoverRating.value
  }
  return ratingValue.value
})

function getStarClass(starIndex) {
  const effectiveRatingValue = effectiveRating.value
  
  if (starIndex <= effectiveRatingValue) {
    return 'text-yellow-400 fill-current'
  }
  return 'text-gray-400'
}

function handleStarClick(starNumber) {
  if (props.editable) {
    emit('rating-changed', starNumber)
  }
}

function handleMouseEnter(starNumber) {
  if (props.editable) {
    hoverRating.value = starNumber
  }
}

function handleMouseLeave() {
  if (props.editable) {
    hoverRating.value = 0
  }
}
</script>
