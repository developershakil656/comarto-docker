import { defineStore } from 'pinia'
import { ref } from 'vue'
import { businessTypeAPI } from '../api/services'

export const useBusinessTypeStore = defineStore('businessType', () => {
  const businessTypes = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await businessTypeAPI.getAll()
      businessTypes.value = response.data.data || []
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch business types'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessTypeAPI.create(data)
      await fetchAll() // Refresh list
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create business type'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessTypeAPI.update(id, data)
      await fetchAll() // Refresh list
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update business type'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await businessTypeAPI.delete(id)
      await fetchAll() // Refresh list
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete business type'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    businessTypes,
    loading,
    error,
    fetchAll,
    create,
    update,
    remove
  }
})
