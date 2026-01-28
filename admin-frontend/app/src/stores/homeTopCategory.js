import { defineStore } from 'pinia'
import { ref } from 'vue'
import { homeTopCategoryAPI } from '../api/services'

export const useHomeTopCategoryStore = defineStore('homeTopCategory', () => {
  const homeTopCategories = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await homeTopCategoryAPI.getAll()
      homeTopCategories.value = response.data.data || []
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch home top categories'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await homeTopCategoryAPI.create(data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create home top category'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getById = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await homeTopCategoryAPI.getById(id)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch home top category details'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await homeTopCategoryAPI.update(id, data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update home top category'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await homeTopCategoryAPI.delete(id)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete home top category'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    homeTopCategories,
    loading,
    error,
    fetchAll,
    create,
    getById,
    update,
    remove
  }
})