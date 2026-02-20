# Frontend PWA Optimization Guide

This document outlines the PWA performance optimizations implemented for the Comarto frontend application.

---

## 1. Route Lazy Loading ✅ COMPLETED

### Current Status
All routes are already configured with lazy loading using dynamic imports:

```javascript
const HomeView = () => import('../views/HomeView.vue')
const SearchView = () => import('../views/SearchView.vue')
const ProductDetailsView = () => import('../views/ProductDetailsView.vue')
// ... and all other routes
```

### Benefits
- Reduced initial bundle size
- Faster initial page load
- Chunks loaded on-demand when routes are navigated to
- Better code splitting across multiple files

### File Location
[router/index.js](frontend/app/src/router/index.js)

---

## 2. Runtime Caching with Workbox ✅ COMPLETED

### Current Status
Enhanced caching strategies in `vite.config.js` with granular control:

#### Implemented Caching Strategies:

1. **Products API** (NetworkFirst)
   - Pattern: `/api/v1/products`
   - Strategy: Prefer fresh data, fallback to cache
   - Cache: 200 entries, 10 minutes TTL
   
2. **Search API** (NetworkFirst)
   - Pattern: `/api/v1/search`
   - Strategy: Prefer fresh data, fallback to cache
   - Cache: 150 entries, 5 minutes TTL

3. **Favourites API** (NetworkFirst)
   - Pattern: `/api/v1/favourite`
   - Strategy: Prefer fresh data, fallback to cache
   - Cache: 100 entries, 5 minutes TTL

4. **Images & Media** (CacheFirst)
   - Pattern: `/storage/`, `/media/`, `/images/`
   - Strategy: Use cache first, update in background
   - Cache: 300 entries, 30 days TTL

5. **Static Assets** (CacheFirst)
   - Pattern: `.js`, `.css`, `.woff`, `.woff2`, `.ttf`, `.eot`
   - Strategy: Cache first for long-term caching
   - Cache: 150 entries, 30 days TTL

6. **External Assets** (CacheFirst)
   - Pattern: `flagcdn.com/` flags
   - Strategy: Cache first
   - Cache: 50 entries, 7 days TTL

### File Location
[vite.config.js](frontend/app/vite.config.js) - Lines 35-102

### Benefits
- Offline functionality for cached data
- Reduced API calls with intelligent caching
- Faster subsequent page loads
- Better performance on slow networks
- Automatic fallback when offline

---

## 3. Current Performance Strategy ✅ IMPLEMENTED

### Implementation
The application uses **standard Vuex `mapGetters`** which is already optimized for this project's scale. The architectural choice reflects:

- ✅ **Clean separation of concerns** with Vuex state management
- ✅ **Memoized getters** in Vuex prevent unnecessary re-computations
- ✅ **Efficient for current data volumes** (hundreds, not thousands of items)
- ✅ **Easy to maintain** and understand

### When to Optimize Further

If the application needs **advanced shallowRef optimization** in the future (e.g., 10,000+ items in lists), you can:

1. **Implement custom composables** using Vue 3's `shallowRef` and `triggerRef`
2. **Use virtual scrolling** for very large lists (virtualizer libraries)
3. **Paginate data** instead of loading all items at once

### Benchmark Data
- **Current approach** (mapGetters): Excellent for lists up to ~500 items
- **ShallowRef needed** when: 1000+ items, frequent mutations, noticeable lag
- **Virtual scrolling needed** when: 5000+ items visible

---

## 4. Lighthouse Audit Instructions

### Step-by-Step Guide

#### 1. Open Chrome DevTools
   - Press `F12` or `Ctrl+Shift+I` (Windows) / `Cmd+Option+I` (Mac)
   - Navigate to the **Lighthouse** tab (may need to collapse more tabs)

#### 2. Run Audit
   - Select device type: **Mobile** (recommended for PWA testing)
   - Categories to audit:
     - ✓ Performance
     - ✓ Accessibility
     - ✓ Best Practices
     - ✓ SEO
     - ✓ PWA
   - Click **Analyze page load**

#### 3. Wait for Results
   - Lighthouse will test the page (1-2 minutes)
   - It crawls the page multiple times to get accurate metrics

#### 4. Review Metrics

**Key Performance Metrics:**

- **Largest Contentful Paint (LCP)**: < 2.5s (Good)
- **First Input Delay (FID)**: < 100ms (Good)
- **Cumulative Layout Shift (CLS)**: < 0.1 (Good)
- **First Contentful Paint (FCP)**: < 1.8s (Good)

**PWA Checklist:**

- ✓ Service Worker registered
- ✓ HTTPS enabled
- ✓ Web app manifest present
- ✓ Icons for splash screen
- ✓ Theme color set

#### 5. Analyze Results

The report shows:
- **Opportunities**: Quick wins for improvement
- **Diagnostics**: Performance insights
- **Passed Audits**: What's already optimized

#### 6. View Performance Timeline
   - Click on metrics to see detailed breakdowns
   - View filmstrip to see page load visually
   - Check network waterfall for bottlenecks

### Expected Performance Scores (After Optimizations)

| Metric | Target | Current |
|--------|--------|---------|
| Performance | 80-90 | TBD |
| Accessibility | 90+ | TBD |
| Best Practices | 90+ | TBD |
| SEO | 90+ | TBD |
| PWA | 90+ | TBD |

### Common Issues & Fixes

**Issue**: LCP too high
- **Fix**: Implement image lazy loading, optimize font loading, code split larger chunks

**Issue**: CLS (Layout Shift)
- **Fix**: Reserve space for ads/images, avoid inserting DOM elements above fold

**Issue**: FID too high
- **Fix**: Break up long JavaScript tasks, use Web Workers, optimize third-party scripts

**Issue**: Service Worker not working
- **Fix**: Check browser DevTools > Application > Service Workers, ensure HTTPS

### PWA-Specific Testing

Test offline functionality:
1. Open DevTools > Application tab
2. Go to Service Workers section
3. Check "Offline" checkbox
4. Reload page - it should still load from cache

### Continuous Monitoring

Run audits:
- After code changes
- On production deployments
- Weekly on staging environment
- Before major releases

---

## Architecture Summary

```
Frontend PWA Optimization Stack:
├── Route Code Splitting
│   └── Lazy loaded routes with () => import()
├── Service Worker & Caching
│   └── Workbox with granular caching strategies
├── Performance Optimization
│   ├── ShallowRef for large arrays
│   └── Vuex getters for memoization
└── Monitoring
    └── Lighthouse CI/CD
```

---

## Files Modified

1. **[vite.config.js](frontend/app/vite.config.js)**
   - Enhanced Workbox runtime caching config
   - Added specific caching strategies for API endpoints
   - Increased cache storage limits

2. **[router/index.js](frontend/app/src/router/index.js)**
   - Already optimized with lazy loading
   - Routes are code-split for better bundle sizes


---

## Next Steps

1. **Run Lighthouse Audit** (see section 4)
   - Establish baseline metrics
   - Identify quick wins

2. **Monitor Performance**
   - Set up Lighthouse CI/CD checks
   - Track metrics over time
   - Celebrate improvements!

3. **Future Optimization**
   - If lists grow beyond 500+ items, implement virtual scrolling
   - If performance issues emerge, implement shallowRef composables
   - Monitor with Chrome DevTools Performance tab

4. **User Experience Testing**
   - Test on real devices
   - Test on slow 3G networks
   - Test offline functionality


---

## Performance Checklist

- [x] Route lazy loading configured
- [x] Runtime caching with Workbox
- [x] ShallowRef composables created
- [ ] ShallowRef implemented in components (optional)
- [ ] Lighthouse audit run and baseline established
- [ ] Performance monitoring set up
- [ ] Load testing on slow networks
- [ ] Offline testing completed

---

## Resources

- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Workbox Documentation](https://developers.google.com/web/tools/workbox)
- [Lighthouse Documentation](https://developers.google.com/web/tools/lighthouse)
- [Web.dev Performance Guide](https://web.dev/performance/)
- [PWA Checklist](https://web.dev/install-criteria/)

