# SEO Implementation Guide

## Backend (Laravel) Implementation

### 1. Sitemap Controller
Created `app/Http/Controllers/SitemapController.php` with:
- Cached sitemap generation (6-hour cache)
- Automatic URL generation for:
  - Static pages (home, categories, legal pages)
  - Categories and subcategories
  - Products (paginated for performance)
  - Businesses (paginated for performance)
- XML sitemap format compliant with sitemap protocol

### 2. Routes
Added to `routes/api/user.php`:
- `GET /api/v1/sitemap` - Returns XML sitemap
- `GET /api/v1/sitemap/count` - Returns URL count

### 3. Dependencies
Added `spatie/laravel-sitemap` to composer.json (though we used native Laravel implementation)

## Frontend (Vue 3) Implementation

### 1. SEO Composable
Created `src/composables/useSEO.js`:
- `setMetaTags()` function for dynamic meta tags
- Handles Open Graph and Twitter cards
- Canonical URL support

### 2. SEO Services
Created `src/services/seoService.js`:
- SEO data generation utilities
- Schema.org structured data generators:
  - Product schema
  - Organization/LocalBusiness schema
  - Breadcrumb schema

### 3. Components
Created reusable SEO components:
- `src/components/SEO/StructuredData.vue` - Adds JSON-LD structured data
- `src/components/SEO/SitemapInfo.vue` - Displays sitemap statistics

### 4. Router Integration
Updated `src/router/index.js`:
- Added SEO metadata to route definitions
- Implemented `afterEach` hook to set meta tags dynamically

### 5. Dependencies
Added `@unhead/vue` to package.json for head management

## How to Use

### For Static Routes
Add metadata to route definitions:
```javascript
{
  path: '/categories',
  name: 'all-categories',
  component: AllCategoriesView,
  meta: {
    title: 'All Categories - B2B Directory Bangladesh',
    description: 'Browse all business categories...'
  }
}
```

### For Dynamic Routes (Product/Business Pages)
Use the SEO composable in your components:

```vue
<script setup>
import { useSEO } from '@/composables/useSEO'
import { seoService } from '@/services/seoService'

const { setMetaTags } = useSEO()

// In your mounted or updated hook
setMetaTags(
  'Product Name - Business Name | B2B Directory Bangladesh',
  'Product description...',
  'featured-image-url.jpg'
)
</script>
```

### Adding Structured Data
Use the StructuredData component:
```vue
<template>
  <StructuredData :data="productSchema" />
</template>

<script setup>
import { seoService } from '@/services/seoService'

const productSchema = seoService.generateProductSchema(product, business)
</script>
```

## Testing

1. **Sitemap**: Visit `http://localhost:8000/api/v1/sitemap`
2. **Sitemap Count**: Visit `http://localhost:8000/api/v1/sitemap/count`
3. **Meta Tags**: Check page source for dynamically generated meta tags
4. **Structured Data**: Use Google's Rich Results Test tool

## Deployment Checklist

1. Update robots.txt with production URLs
2. Configure proper cache headers for sitemap endpoint
3. Set up monitoring for sitemap generation
4. Submit sitemap to Google Search Console
5. Test all SEO features in production environment

## File Structure Summary

```
backend/app/
├── app/Http/Controllers/SitemapController.php
├── routes/api/user.php

frontend/app/
├── src/
│   ├── composables/useSEO.js
│   ├── services/
│   │   ├── seoService.js
│   │   └── sitemapService.js
│   ├── components/SEO/
│   │   ├── StructuredData.vue
│   │   └── SitemapInfo.vue
│   └── router/index.js
├── public/robots.txt
```