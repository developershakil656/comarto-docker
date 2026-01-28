<template>
    <li class="category-item">
      <a :href="`/product?category=${category.slug}`" class="category-menu">
        <span class="text-sm font-medium capitalize">{{ category.name }}</span>
        <ChevronRightIcon v-if="hasChildren" class="inline-block h-4 w-4 ml-2" />
      </a>
  
      <ul v-if="hasChildren" class="category-nest">
        <CategoryLoop
          v-for="(child, index) in category.children"
          :key="index"
          :category="child"
        />
      </ul>
    </li>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import CategoryLoop from './CategoryLoop.vue'
  import { ChevronRightIcon } from '@heroicons/vue/20/solid'
  
  const props = defineProps({
    category: {
      type: Object,
      required: true,
    },
  })
  
  const hasChildren = computed(() => {
    return props.category.children && props.category.children.length > 0
  })
  </script>