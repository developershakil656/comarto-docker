import { defineStore } from 'pinia'
import { ref } from 'vue'
import { accountVerificationAPI } from '../api/services'

export const useAccountVerificationStore = defineStore('accountVerification', () => {
  const verifications = ref([])
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
      const response = await accountVerificationAPI.getAll(params)
      verifications.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch account verifications'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStatus = async (id, status) => {
    loading.value = true
    error.value = null
    try {
      const response = await accountVerificationAPI.updateStatus(id, { status })
      // Update the verification in the list
      const index = verifications.value.findIndex(verification => verification.id === id)
      if (index !== -1) {
        verifications.value[index].status = status
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update account verification status'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    verifications,
    loading,
    error,
    pagination,
    fetchAll,
    updateStatus
  }
})