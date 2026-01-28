import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productAPI } from '../api/services'

export const useProductStore = defineStore('product', () => {
  const products = ref([])
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
      const response = await productAPI.getAll(params)
      products.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch products'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateStatus = async (id, status) => {
    loading.value = true
    error.value = null
    try {
      const response = await productAPI.updateStatus(id, { status })
      // Update the product in the list
      const index = products.value.findIndex(product => product.id === id)
      if (index !== -1) {
        products.value[index] = { ...products.value[index], ...response.data.data }
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update product status'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    products,
    loading,
    error,
    pagination,
    fetchAll,
    updateStatus
  }
})