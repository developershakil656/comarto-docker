<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
    <div class="max-w-md w-full text-center">
      <div class="text-9xl font-bold text-primary mb-4">404</div>
      <h1 class="text-3xl font-bold text-gray-800 mb-4">Page Not Found</h1>
      <p class="text-gray-600 mb-8">
        Sorry, the page you're looking for doesn't exist or has been moved.
      </p>
      <div class="space-y-4">
        <router-link 
          :to="{ name: 'home' }" 
          class="inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary/85 transition"
        >
          Go to Homepage
        </router-link>
        <div class="mt-6">
          <router-link 
            :to="{ name: 'all-categories' }" 
            class="text-primary hover:underline"
          >
            Browse Categories
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useSEO } from '@/composables/useSEO';

export default {
  name: 'NotFoundView',
  async mounted() {
    // Set SEO meta tags for the 404 page
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Page Not Found - Comarto',
      'The page you are looking for does not exist. Return to Comarto homepage to find what you need.',
      null,
      'Comarto, 404, page not found, error, Bangladesh, B2B, marketplace'
    );
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>