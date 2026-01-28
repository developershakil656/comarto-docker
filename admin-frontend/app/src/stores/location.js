import { defineStore } from 'pinia'
import { ref } from 'vue'
import { locationAPI } from '../api/services'

export const useLocationStore = defineStore('location', () => {
  const locations = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await locationAPI.getAll()
      locations.value = response.data.data || []
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch locations'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await locationAPI.create(data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create location'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await locationAPI.update(id, data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update location'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await locationAPI.delete(id)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete location'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    locations,
    loading,
    error,
    fetchAll,
    create,
    update,
    remove
  }
})
