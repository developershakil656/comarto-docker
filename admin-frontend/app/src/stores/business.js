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

  return {
    businesses,
    loading,
    error,
    pagination,
    fetchAll,
    updateStatus,
    getBusinessProducts
  }
})