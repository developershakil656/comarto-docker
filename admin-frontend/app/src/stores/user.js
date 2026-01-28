import { defineStore } from 'pinia'
import { ref } from 'vue'
import { userAPI } from '../api/services'

export const useUserStore = defineStore('user', () => {
  const users = ref([])
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
      const response = await userAPI.getAll(params)
      users.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch users'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStatus = async (id, status) => {
    loading.value = true
    error.value = null
    try {
      const response = await userAPI.updateStatus(id, { status })
      // Update the user in the list
      const index = users.value.findIndex(user => user.id === id)
      if (index !== -1) {
        users.value[index] = { ...users.value[index], ...response.data.data }
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update user status'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    users,
    loading,
    error,
    pagination,
    fetchAll,
    updateStatus
  }
})