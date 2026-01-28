import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productUnitAPI } from '../api/services'

export const useProductUnitStore = defineStore('productUnit', () => {
  const productUnits = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await productUnitAPI.getAll()
      productUnits.value = response.data.data || []
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch product units'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await productUnitAPI.create(data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create product unit'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await productUnitAPI.update(id, data)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update product unit'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await productUnitAPI.delete(id)
      await fetchAll()
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete product unit'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    productUnits,
    loading,
    error,
    fetchAll,
    create,
    update,
    remove
  }
})
