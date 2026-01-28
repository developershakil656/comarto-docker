import { defineStore } from 'pinia'
import { ref } from 'vue'
import { leadCreditPurchaseAPI } from '../api/services'

export const useLeadCreditPurchaseStore = defineStore('leadCreditPurchase', () => {
  const purchases = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0
  })

  const fetchAll = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await leadCreditPurchaseAPI.getAll(params)
      purchases.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch lead credit purchases'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStatus = async (id, status) => {
    loading.value = true
    error.value = null
    try {
      const response = await leadCreditPurchaseAPI.updateStatus(id, { status })
      // Update the purchase in the list
      const index = purchases.value.findIndex(purchase => purchase.id === id)
      if (index !== -1) {
        purchases.value[index] = { ...purchases.value[index], ...response.data.data }
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update purchase status'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    purchases,
    loading,
    error,
    pagination,
    fetchAll,
    updateStatus
  }
})