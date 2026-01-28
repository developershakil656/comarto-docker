import { defineStore } from 'pinia'
import { ref } from 'vue'
import { leadCreditPackageAPI } from '../api/services'

export const useLeadCreditPackageStore = defineStore('leadCreditPackage', () => {
  const packages = ref([])
  const currentPackage = ref(null)
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
      const response = await leadCreditPackageAPI.getAll(params)
      packages.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.meta.current_page,
        last_page: response.data.data.meta.last_page,
        per_page: response.data.data.meta.per_page,
        total: response.data.data.meta.total
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch lead credit packages'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getById = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await leadCreditPackageAPI.getById(id)
      currentPackage.value = response.data.data
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch lead credit package'
      throw err
    } finally {
      loading.value = false
    }
  }

  const create = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await leadCreditPackageAPI.create(data)
      await fetchAll() // Refresh list
      return response
    } catch (err) {
      error.value = err.response?.data?.message || err.response?.data?.data || 'Failed to create lead credit package'
      throw err
    } finally {
      loading.value = false
    }
  }

  const update = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await leadCreditPackageAPI.update(id, data)
      await fetchAll() // Refresh list
      return response
    } catch (err) {
      error.value = err.response?.data?.message || err.response?.data?.data || 'Failed to update lead credit package'
      throw err
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await leadCreditPackageAPI.delete(id)
      await fetchAll() // Refresh list
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete lead credit package'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    packages,
    currentPackage,
    loading,
    error,
    pagination,
    fetchAll,
    getById,
    create,
    update,
    remove
  }
})