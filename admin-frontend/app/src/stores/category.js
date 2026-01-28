import { defineStore } from 'pinia'
import { ref } from 'vue'
import { categoryAPI } from '../api/services'

export const useCategoryStore = defineStore('category', () => {
  const categories = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await categoryAPI.getAll()
      categories.value = response.data.data || []
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch categories'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await categoryAPI.create(data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create category'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await categoryAPI.update(id, data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update category'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await categoryAPI.delete(id)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete category'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    categories,
    loading,
    error,
    fetchAll,
    create,
    update,
    remove
  }
})
