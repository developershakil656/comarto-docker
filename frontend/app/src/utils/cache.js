/**
 * Cache Management Utility for PWA Offline Support
 * Stores API responses in IndexedDB and localStorage for offline access
 */

const DB_NAME = 'comarto-cache';
const DB_VERSION = 1;
const CACHE_STORES = {
  api: 'api_cache',
  search: 'search_cache',
  products: 'products_cache',
  business: 'business_cache',
  user: 'user_cache',
  conversations: 'conversations_cache',
  inquiries: 'inquiries_cache',
  leads: 'leads_cache',
};

const CACHE_DURATION = {
  // increased defaults so that a typical user can go offline for a few
  // hours without losing their data; change these values to suit your
  // application's needs.
  default: 12 * 60 * 60 * 1000, // 12 hours
  search: 10 * 60 * 1000, // short-lived because results are ephemeral
  products: 24 * 60 * 60 * 1000, // 1 day - product details shouldn't change often
  business: 24 * 60 * 60 * 1000, // 1 day
  user: 6 * 60 * 60 * 1000, // 6 hours
  conversations: 5 * 60 * 1000, // 5 minutes
  inquiries: 10 * 60 * 1000, // 10 minutes
  leads: 10 * 60 * 1000, // 10 minutes
};

class CacheManager {
  constructor() {
    this.db = null;
    this.isOnline = typeof navigator !== 'undefined' ? navigator.onLine : true;
    this.initDB();
    this.setupOnlineListeners();
  }

  setupOnlineListeners() {
    if (typeof window !== 'undefined') {
      window.addEventListener('online', () => {
        this.isOnline = true;
        console.log('App is online');
      });
      window.addEventListener('offline', () => {
        this.isOnline = false;
        console.log('App is offline - using cached data');
      });
    }
  }

  async initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, DB_VERSION);

      request.onerror = () => {
        console.error('Failed to open IndexedDB');
        reject(request.error);
      };

      request.onsuccess = () => {
        this.db = request.result;
        resolve(this.db);
      };

      request.onupgradeneeded = (event) => {
        const db = event.target.result;
        
        // Create object stores for different cache types
        Object.values(CACHE_STORES).forEach(storeName => {
          if (!db.objectStoreNames.contains(storeName)) {
            const store = db.createObjectStore(storeName, { keyPath: 'url' });
            store.createIndex('timestamp', 'timestamp', { unique: false });
          }
        });
      };
    });
  }

  /**
   * Determine cache store and duration based on API endpoint
   */
  getCacheConfig(url) {
    if (url.includes('/search')) {
      return { store: CACHE_STORES.search, duration: CACHE_DURATION.search };
    }
    if (url.includes('/product')) {
      return { store: CACHE_STORES.products, duration: CACHE_DURATION.products };
    }
    if (url.includes('/business')) {
      return { store: CACHE_STORES.business, duration: CACHE_DURATION.business };
    }
    if (url.includes('/user') && !url.includes('/conversations') && !url.includes('/inquiries')) {
      return { store: CACHE_STORES.user, duration: CACHE_DURATION.user };
    }
    if (url.includes('/conversations')) {
      return { store: CACHE_STORES.conversations, duration: CACHE_DURATION.conversations };
    }
    if (url.includes('/inquiries')) {
      return { store: CACHE_STORES.inquiries, duration: CACHE_DURATION.inquiries };
    }
    if (url.includes('/leads')) {
      return { store: CACHE_STORES.leads, duration: CACHE_DURATION.leads };
    }
    return { store: CACHE_STORES.api, duration: CACHE_DURATION.default };
  }

  /**
   * Set cache data for a URL
   */
  async setCache(url, data, customDuration = null) {
    if (!this.db) {
      try {
        await this.initDB();
      } catch (error) {
        console.warn('Could not initialize IndexedDB', error);
        return;
      }
    }

    const config = this.getCacheConfig(url);
    const duration = customDuration || config.duration;

    const cacheEntry = {
      url,
      data,
      timestamp: Date.now(),
      expiresAt: Date.now() + duration,
    };

    // also keep a simple JSON copy in localStorage as a very lightweight
    // fallback.  IndexedDB can be blocked or unavailable in some environments
    // (private browsing), so this provides a secondary store so offline works
    // more reliably.
    try {
      localStorage.setItem(`cache:${url}`, JSON.stringify(cacheEntry));
    } catch (e) {
      // quota or unavailable; ignore
    }

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([config.store], 'readwrite');
      const store = transaction.objectStore(config.store);
      const request = store.put(cacheEntry);

      request.onerror = () => {
        console.warn(`Failed to cache: ${url}`, request.error);
        reject(request.error);
      };

      request.onsuccess = () => {
        resolve(cacheEntry);
      };
    });
  }

  /**
   * Get cached data for a URL
   */
  async getCache(url) {
    if (!this.db) {
      try {
        await this.initDB();
      } catch (error) {
        console.warn('Could not initialize IndexedDB', error);
        return null;
      }
    }

    const config = this.getCacheConfig(url);

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([config.store], 'readonly');
      const store = transaction.objectStore(config.store);
      const request = store.get(url);

      request.onerror = () => {
        console.warn(`Failed to retrieve cache: ${url}`, request.error);
        resolve(null);
      };

      request.onsuccess = () => {
        let cacheEntry = request.result;

        if (!cacheEntry) {
          // try localStorage fallback
          try {
            const local = localStorage.getItem(`cache:${url}`);
            if (local) cacheEntry = JSON.parse(local);
          } catch {}
        }

        if (!cacheEntry) {
          resolve(null);
          return;
        }

        // Check if cache has expired
        if (cacheEntry.expiresAt && cacheEntry.expiresAt < Date.now()) {
          // Delete expired cache
          this.removeCache(url).catch(err => console.warn('Failed to remove expired cache', err));
          resolve(null);
          return;
        }

        resolve(cacheEntry.data);
      };
    });
  }

  /**
   * Remove cached data for a URL
   */
  async removeCache(url) {
    if (!this.db) {
      try {
        await this.initDB();
      } catch (error) {
        console.warn('Could not initialize IndexedDB', error);
        return;
      }
    }

    const config = this.getCacheConfig(url);

    // also clear from localStorage
    try { localStorage.removeItem(`cache:${url}`); } catch {}

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([config.store], 'readwrite');
      const store = transaction.objectStore(config.store);
      const request = store.delete(url);

      request.onerror = () => {
        console.warn(`Failed to remove cache: ${url}`, request.error);
        reject(request.error);
      };

      request.onsuccess = () => {
        resolve();
      };
    });
  }

  /**
   * Clear all cache for a specific store
   */
  async clearCache(storeName = null) {
    if (!this.db) {
      try {
        await this.initDB();
      } catch (error) {
        console.warn('Could not initialize IndexedDB', error);
        return;
      }
    }

    const stores = storeName ? [storeName] : Object.values(CACHE_STORES);

    // Use Promise.all to clear all requested stores instead of returning after
    // the first one.  previously the loop returned on first iteration which
    // meant only the first store ever got cleared.
    const promises = stores.map((store) => {
      return new Promise((resolve, reject) => {
        const transaction = this.db.transaction([store], 'readwrite');
        const objectStore = transaction.objectStore(store);
        const request = objectStore.clear();

        request.onerror = () => {
          console.warn(`Failed to clear cache store: ${store}`, request.error);
          reject(request.error);
        };

        request.onsuccess = () => {
          resolve();
        };
      });
    });

    return Promise.all(promises);
  }

  /**
   * Get all cached keys for a store
   */
  async getCacheKeys(storeName) {
    if (!this.db) {
      try {
        await this.initDB();
      } catch (error) {
        console.warn('Could not initialize IndexedDB', error);
        return [];
      }
    }

    return new Promise((resolve, reject) => {
      // if indexedDB is not available we can't really enumerate, but we still
      // allow callers to try to read a specific key later
      const transaction = this.db.transaction([storeName], 'readonly');
      const store = transaction.objectStore(storeName);
      const request = store.getAllKeys();

      request.onerror = () => {
        console.warn(`Failed to get cache keys: ${storeName}`, request.error);
        resolve([]);
      };

      request.onsuccess = () => {
        resolve(request.result);
      };
    });
  }

  /**
   * Check if device is online
   */
  isDeviceOnline() {
    return this.isOnline;
  }
}

// Export singleton instance
export const cacheManager = new CacheManager();

export default cacheManager;
