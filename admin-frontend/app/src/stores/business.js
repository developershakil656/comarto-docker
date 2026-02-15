import { defineStore } from 'pinia'
import { ref } from 'vue'
import { businessAPI } from '../api/services'

export const useBusinessStore = defineStore('business', () => {
  const businesses = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  })

  const fetchAll = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessAPI.getAll(params)
      businesses.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch businesses'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStatus = async (id, status) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessAPI.updateStatus(id, { status })
      // Update the business in the list
      const index = businesses.value.findIndex(business => business.id === id)
      if (index !== -1) {
        businesses.value[index] = { ...businesses.value[index], ...response.data.data }
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update business status'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getBusinessProducts = async (id, params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessAPI.getBusinessProducts(id, params)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch business products'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getById = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessAPI.getById(id)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch business'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      let config = {};
      
      if (data instanceof FormData) {
        config.headers = {
          'Content-Type': 'multipart/form-data'
        };
      }
      const response = await businessAPI.create(data, config)
      // Refresh the businesses list
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create business'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      // Check if data is FormData (for file uploads) or regular object
      let requestData = data;
      let config = {};
      
      if (data instanceof FormData) {
        config.headers = {
          'Content-Type': 'multipart/form-data'
        };
      }
      
      const response = await businessAPI.update(id, requestData, config)
      // Update the business in the list
      const index = businesses.value.findIndex(business => business.id === id)
      if (index !== -1) {
        businesses.value[index] = { ...businesses.value[index], ...response.data.data }
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update business'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessAPI.delete(id)
      // Remove the business from the list
      businesses.value = businesses.value.filter(business => business.id !== id)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete business'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    businesses,
    loading,
    error,
    pagination,
    fetchAll,
    getById,
    updateStatus,
    getBusinessProducts,
    create,
    update,
    remove
  }
})