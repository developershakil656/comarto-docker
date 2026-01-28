import { defineStore } from 'pinia'
import { ref } from 'vue'
import { leadAPI } from '../api/services'

export const useLeadStore = defineStore('lead', () => {
  const leads = ref([])
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
      const response = await leadAPI.getAll(params)
      leads.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch leads'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    leads,
    loading,
    error,
    pagination,
    fetchAll
  }
})