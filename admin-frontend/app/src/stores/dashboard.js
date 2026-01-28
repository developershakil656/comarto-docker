import { defineStore } from 'pinia'
import { ref } from 'vue'
import { dashboardAPI } from '../api/services'

export const useDashboardStore = defineStore('dashboard', () => {
  const summary = ref({})
  const todayStats = ref({})
  const weeklyGraph = ref({})
  const loading = ref(false)
  const error = ref(null)

  const fetchSummary = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await dashboardAPI.getSummary()
      summary.value = response.data.data || {}
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch dashboard summary'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchTodayStats = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await dashboardAPI.getTodayStats()
      todayStats.value = response.data.data || {}
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch today stats'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchWeeklyGraph = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await dashboardAPI.getWeeklyGraph()
      weeklyGraph.value = response.data || {}
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch weekly graph data'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchAllDashboardData = async () => {
    loading.value = true
    error.value = null
    try {
      await Promise.all([
        fetchSummary(),
        fetchTodayStats(),
        fetchWeeklyGraph()
      ])
    } catch (err) {
      error.value = 'Failed to fetch dashboard data'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    summary,
    todayStats,
    weeklyGraph,
    loading,
    error,
    fetchSummary,
    fetchTodayStats,
    fetchWeeklyGraph,
    fetchAllDashboardData
  }
})