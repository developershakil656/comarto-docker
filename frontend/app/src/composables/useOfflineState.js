/**
 * Composable for offline state management
 * Provides reactive offline status for Vue components
 */

import { ref, onMounted, onUnmounted } from 'vue';
import { cacheManager } from '@/utils/cache';

export function useOfflineState() {
  const isOffline = ref(false);
  const isUsingCache = ref(false);

  const updateOnlineStatus = () => {
    isOffline.value = !cacheManager.isDeviceOnline();
  };

  const setUsingCache = (value) => {
    isUsingCache.value = value;
  };

  onMounted(() => {
    // Set initial status
    updateOnlineStatus();

    // Listen for online/offline events
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
  });

  onUnmounted(() => {
    window.removeEventListener('online', updateOnlineStatus);
    window.removeEventListener('offline', updateOnlineStatus);
  });

  return {
    isOffline,
    isUsingCache,
    setUsingCache,
  };
}

export default useOfflineState;
