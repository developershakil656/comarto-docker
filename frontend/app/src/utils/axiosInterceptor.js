/**
 * Axios Interceptor for Offline Cache Support
 * Automatically caches responses and serves cached data when offline
 */

import { cacheManager } from './cache';

/**
 * Normalize a fully‑qualified URL for caching.  The function sorts any query
 * parameters so that `/foo?a=1&b=2` and `/foo?b=2&a=1` both map to the same
 * cache key.  The host and protocol are ignored because only the path matters
 * for our API endpoints.
 */
function normalizeUrlForCache(url) {
  try {
    const urlObj = new URL(url, 'http://localhost');
    const params = new URLSearchParams(urlObj.search);
    const sortedParams = new URLSearchParams([...params.entries()].sort());
    return urlObj.pathname + (sortedParams.toString() ? '?' + sortedParams.toString() : '');
  } catch {
    // In case URL constructor throws (e.g. odd relative value), fall back to a
    // simple split rather than crashing the entire interceptor.
    return url.split('?')[0] + (url.includes('?') ? '?' + url.split('?')[1] : '');
  }
}

// Build a consistent cache key from the axios request configuration.  This
// handles the following cases that were previously leading to cache misses:
//
// * `config.baseURL` being prepended during the request, producing a different
//   string in the response/error config than the one used for caching.
// * Passing `config.params` instead of embedding a query string in `url`.
//
// The returned string is suitable for feeding into `normalizeUrlForCache()`.
function getCacheKey(config) {
  let { url = '' } = config;
  // If the user provided params, append them to the url so that they are
  // included in the key.  Axios doesn't put params in the `url` property, so
  // we have to do it ourselves.
  if (config.params) {
    const searchParams = new URLSearchParams(config.params).toString();
    if (searchParams) {
      url += (url.includes('?') ? '&' : '?') + searchParams;
    }
  }
  // Combine with baseURL if the provided url is relative.  normalizeUrlForCache
  // will drop the host anyway, but this ensures we generate identical strings
  // for requests that started out as '/foo' and were later rewritten to
  // 'https://api/.../foo' by axios.
  if (config.baseURL && !/^(https?:)?\/\//.test(url)) {
    url = config.baseURL.replace(/\/$/, '') + '/' + url.replace(/^\//, '');
  }
  return normalizeUrlForCache(url);
}

export function setupAxiosInterceptors(axiosInstance) {
  /**
   * Response interceptor - cache successful responses
   */
  axiosInstance.interceptors.response.use(
    async (response) => {
      // Cache successful GET requests only
      if (response.config.method === 'get' && response.status === 200) {
        try {
          const cacheUrl = getCacheKey(response.config);
          await cacheManager.setCache(cacheUrl, response.data);
          console.log('Cached response for:', cacheUrl);
        } catch (error) {
          console.warn('Failed to cache response:', error);
        }
      }
      return response;
    },
    async (error) => {
      // if the request interceptor deliberately rejected in order to serve
      // a cached response, just propagate that response back to the caller.
      if (error && error.__fromCache && error.response) {
        return Promise.resolve(error.response);
      }

      // On actual network or server errors, try to return cached data for GET
      // requests.
      if (error.config && error.config.method === 'get') {
        try {
          const cacheUrl = getCacheKey(error.config);
          const cachedData = await cacheManager.getCache(cacheUrl);

          if (cachedData) {
            console.log('Returning cached data for:', cacheUrl);
            // Return cached data as if it were a successful response
            return Promise.resolve({
              data: cachedData,
              status: 200,
              statusText: 'OK (from cache)',
              headers: error.response?.headers || {},
              config: error.config,
              cached: true,
            });
          }
        } catch (cacheError) {
          console.warn('Failed to retrieve cached data:', cacheError);
        }
      }

      // If no cache available, reject with original error
      return Promise.reject(error);
    }
  );

  // Optional: proactively serve cached data when the app is offline so the
  // request never has to hit the network and trigger an error.
  axiosInstance.interceptors.request.use(async (config) => {
    if (
      config.method === 'get' &&
      !cacheManager.isDeviceOnline() &&
      config.url // guard in case config is weird
    ) {
      try {
        const cacheUrl = getCacheKey(config);
        const cachedData = await cacheManager.getCache(cacheUrl);
        if (cachedData) {
          console.log('Serving cached data (offline) for:', cacheUrl);
          // mimic axios response structure so caller code doesn't notice
          // include the original config on the response too so callers
          // (or later interceptors) can inspect it if needed.
          const fakeResponse = {
            data: cachedData,
            status: 200,
            statusText: 'OK (from cache)',
            headers: {},
            config,
          };
          return Promise.reject({
            __fromCache: true,
            config,
            response: fakeResponse,
          });
        }
      } catch (e) {
        // ignore – we'll just let the request proceed and fail as usual
      }
    }
    return config;
  });
}

export default setupAxiosInterceptors;
