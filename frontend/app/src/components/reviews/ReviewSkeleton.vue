<template>
  <div>
    <!-- Start Review Section Skeleton -->
    <div class="mb-4 sm:mb-5 p-3 sm:p-4 rounded-md shadow border">
      <div class="h-5 sm:h-6 bg-gray-200 rounded w-1/3 sm:w-1/4 mb-3 sm:mb-4 animate-pulse"></div>
      <div class="space-y-2 sm:space-y-3">
        <div class="h-8 sm:h-10 bg-gray-200 rounded w-full animate-pulse"></div>
        <div class="h-16 sm:h-20 bg-gray-200 rounded w-full animate-pulse"></div>
        <div class="h-8 sm:h-10 bg-gray-200 rounded w-1/4 sm:w-1/3 animate-pulse"></div>
      </div>
    </div>

    <!-- Rating Section Skeleton -->
    <div class="flex items-center mb-3 sm:mb-4">
      <div class="bg-gray-200 rounded-md text-2xl sm:text-3xl font-bold mr-3 sm:mr-4 animate-pulse">
        <div class="px-2 py-2 sm:py-3 w-12 sm:w-14 h-10 sm:h-12 bg-gray-200 rounded"></div>
      </div>
      <div class="flex-1 min-w-0">
        <div class="mb-1 sm:mb-2">
          <div class="h-6 sm:h-7 bg-gray-200 rounded w-24 sm:w-28 animate-pulse"></div>
        </div>
        <div class="text-sm sm:text-base">
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-24 animate-pulse"></div>
        </div>
      </div>
    </div>

    <!-- Filter Buttons Skeleton -->
    <div class="flex space-x-1 sm:space-x-2 mb-3 sm:mb-4 flex-wrap gap-1 sm:gap-2">
      <div class="h-6 sm:h-8 bg-gray-200 rounded w-12 sm:w-16 animate-pulse"></div>
      <div class="h-6 sm:h-8 bg-gray-200 rounded w-20 sm:w-24 animate-pulse"></div>
      <div class="h-6 sm:h-8 bg-gray-200 rounded w-20 sm:w-24 animate-pulse"></div>
      <div class="h-6 sm:h-8 bg-gray-200 rounded w-24 sm:w-32 animate-pulse"></div>
    </div>

    <!-- Reviews Skeleton -->
    <SkeletonLoader type="reviews" :count="3" />
  </div>
</template>

<script>
import SkeletonLoader from '@/components/SkeletonLoader.vue'

export default {
  name: 'ReviewSkeleton',
  components: {
    SkeletonLoader
  }
}
</script> 