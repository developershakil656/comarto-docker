import apiClient from './axios'

// Admin Authentication
export const adminAPI = {
  login: (credentials) => apiClient.post('/login', credentials),
  logout: () => apiClient.post('/logout'),
  update: (data) => apiClient.put('/update', data),
  updateProfilePicture: (data) => apiClient.post('/profile', data),
  changePassword: (data) => apiClient.post('/password/change', data)
}

// Dashboard APIs
export const dashboardAPI = {
  getSummary: () => apiClient.get('/dashboard/summary'),
  getTodayStats: () => apiClient.get('/dashboard/today/stats'),
  getWeeklyGraph: () => apiClient.get('/dashboard/weekly/graph')
}

// Business Types
export const businessTypeAPI = {
  getAll: () => apiClient.get('/business/types'),
  getById: (id) => apiClient.get(`/business/types/${id}`),
  create: (data) => apiClient.post('/business/types', data),
  update: (id, data) => apiClient.put(`/business/types/${id}`, data),
  delete: (id) => apiClient.delete(`/business/types/${id}`)
}

// Categories
export const categoryAPI = {
  getAll: () => apiClient.get('/categories'),
  getById: (id) => apiClient.get(`/categories/${id}`),
  create: (data) => apiClient.post('/categories', data),
  update: (id, data) => apiClient.post(`/categories/update/${id}`, data),
  delete: (id) => apiClient.delete(`/categories/${id}`),
  productCategories: (data) => apiClient.post('/product/categories', data)
}

// Locations
export const locationAPI = {
  getAll: () => apiClient.get('/locations'),
  search: (keyword) => apiClient.post('/business/locations/search', { keyword }),
  getById: (id) => apiClient.get(`/locations/${id}`),
  create: (data) => apiClient.post('/locations', data),
  update: (id, data) => apiClient.put(`/locations/${id}`, data),
  delete: (id) => apiClient.delete(`/locations/${id}`)
}

// Product Units
export const productUnitAPI = {
  getAll: () => apiClient.get('/product/units'),
  getById: (id) => apiClient.get(`/product/units/${id}`),
  create: (data) => apiClient.post('/product/units', data),
  update: (id, data) => apiClient.put(`/product/units/${id}`, data),
  delete: (id) => apiClient.delete(`/product/units/${id}`)
}

// Lead Credit Packages
export const leadCreditPackageAPI = {
  getAll: (params) => apiClient.get('/lead/credit/packages', { params }),
  getById: (id) => apiClient.get(`/lead/credit/packages/${id}`),
  create: (data) => apiClient.post('/lead/credit/packages', data),
  update: (id, data) => apiClient.put(`/lead/credit/packages/${id}`, data),
  delete: (id) => apiClient.delete(`/lead/credit/packages/${id}`)
}

// Lead Credit Purchases
export const leadCreditPurchaseAPI = {
  getAll: (params) => apiClient.get('/lead/credit/purchases', { params }),
  updateStatus: (id, data) => apiClient.put(`/lead/credit/purchases/${id}/status`, data)
}

// Businesses
export const businessAPI = {
  getAll: (params) => apiClient.get('/businesses', { params }),
  getById: (id) => apiClient.get(`/businesses/${id}`),
  // ensure config is provided with a default to avoid ReferenceError
  create: (data, config = {}) => apiClient.post('/businesses', data, config),
  update: (id, data, config = {}) => apiClient.post(`/businesses/${id}`, data, config),
  updateStatus: (id, data) => apiClient.put(`/businesses/${id}/status`, data),
  delete: (id) => apiClient.delete(`/businesses/${id}`),
  getBusinessProducts: (id, params) => apiClient.get(`/businesses/${id}/products`, { params })
}

// Users
export const userAPI = {
  getAll: (params) => apiClient.get('/users', { params }),
  updateStatus: (id, data) => apiClient.put(`/users/${id}/status`, data)
}

// Products
export const productAPI = {
  getAll: (params) => apiClient.get('/products', { params }),
  getById: (id) => apiClient.get(`/products/${id}`),
  create: (data) => apiClient.post('/products', data),
  update: (id, data) => apiClient.put(`/products/${id}`, data),
  delete: (id) => apiClient.delete(`/products/${id}`),
  uploadImages: (id, formData) => apiClient.post(`/products/${id}/images`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  deleteImage: (imageId) => apiClient.delete(`/products/images/${imageId}`),
  reorderImages: (id, payload) => apiClient.put(`/products/${id}/images/positions`, payload),
  updateStatus: (id, data) => apiClient.put(`/products/${id}/status`, data)
}

// Leads
export const leadAPI = {
  getAll: (params) => apiClient.get('/leads', { params })
}

// Account Verifications
export const accountVerificationAPI = {
  getAll: (params) => apiClient.get('/account/verifications', { params }),
  updateStatus: (id, data) => apiClient.put(`/account/verifications/${id}/status`, data)
}

// Home Top Categories
export const homeTopCategoryAPI = {
  getAll: () => apiClient.get('/home/top/categories'),
  getById: (id) => apiClient.get(`/home/top/categories/${id}`),
  create: (data) => apiClient.post('/home/top/categories', data),
  update: (id, data) => apiClient.put(`/home/top/categories/${id}`, data),
  delete: (id) => apiClient.delete(`/home/top/categories/${id}`)
}

// Business Utilities
export const businessUtilityAPI = {
  checkSlugAvailability: (slug) => apiClient.post('/business/slug/check', { slug })
}
