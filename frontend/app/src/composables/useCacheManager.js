/**
 * Composable for cache management in components
 * Provides methods to interact with the cache
 */

import { cacheManager } from '@/utils/cache';

export function useCacheManager() {
  /**
   * Clear specific cache store
   */
  const clearCacheStore = async (storeName) => {
    try {
      await cacheManager.clearCache(storeName);
      console.log(`Cleared cache store: ${storeName}`);
    } catch (error) {
      console.error('Failed to clear cache store:', error);
      throw error;
    }
  };

  /**
   * Clear all cache
   */
  const clearAllCache = async () => {
    try {
      // Get all store names and clear them
      const stores = [
        'api_cache',
        'search_cache',
        'products_cache',
        'business_cache',
        'user_cache',
        'conversations_cache',
        'inquiries_cache',
        'leads_cache',
      ];

      for (const store of stores) {
        await cacheManager.clearCache(store);
      }
      console.log('Cleared all cache');
    } catch (error) {
      console.error('Failed to clear all cache:', error);
      throw error;
    }
  };

  /**
   * Get cache size
   */
  const getCacheSize = async () => {
    const stores = [
      'api_cache',
      'search_cache',
      'products_cache',
      'business_cache',
      'user_cache',
      'conversations_cache',
      'inquiries_cache',
      'leads_cache',
    ];

    let totalSize = 0;
    const cacheBreakdown = {};

    for (const store of stores) {
      const keys = await cacheManager.getCacheKeys(store);
      cacheBreakdown[store] = keys.length;
      totalSize += keys.length;
    }

    return { totalSize, breakdown: cacheBreakdown };
  };

  /**
   * Remove specific URL from cache
   */
  const removeCacheUrl = async (url) => {
    try {
      await cacheManager.removeCache(url);
      console.log(`Removed cache for URL: ${url}`);
    } catch (error) {
      console.error('Failed to remove cache URL:', error);
      throw error;
    }
  };

  return {
    clearCacheStore,
    clearAllCache,
    getCacheSize,
    removeCacheUrl,
  };
}

export default useCacheManager;
