<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Total Users</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.userCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Businesses</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.businessCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Products</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.productCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10h4"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Conversations</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.conversationCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Leads</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.leadCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
        </div>
      </div>

      <router-link to="/lead-credit-purchases" class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Credit Purchase Pending</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.creditPurchacePendingCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
          </div>
        </div>
      </router-link>
      
      <router-link to="/account-verifications" class="bg-white rounded-2xl shadow-sm p-6 admin-card">
            <p class="text-sm font-medium text-gray-600">Verification Pending</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ dashboardSummary.verificationPendingCount || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
        </div>
      </router-link>

      <a href="https://bulksmsbd.net" target="_blank" class="bg-white rounded-2xl shadow-sm p-6 admin-card">
        <p class="text-sm font-medium text-gray-600">SMS Balance</p>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formattedSmsBalance }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
          </div>
        </div>
      </a>
    </div>

    <!-- Today's Stats -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Today's Statistics</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="border border-gray-200 rounded-lg p-4 text-center admin-card">
          <p class="text-sm text-gray-600">Users</p>
          <p class="text-2xl font-bold text-indigo-600">{{ todayStats.user || 0 }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 text-center admin-card">
          <p class="text-sm text-gray-600">Businesses</p>
          <p class="text-2xl font-bold text-purple-600">{{ todayStats.business || 0 }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 text-center admin-card">
          <p class="text-sm text-gray-600">Products</p>
          <p class="text-2xl font-bold text-green-600">{{ todayStats.product || 0 }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 text-center admin-card">
          <p class="text-sm text-gray-600">Leads</p>
          <p class="text-2xl font-bold text-yellow-600">{{ todayStats.lead || 0 }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 text-center admin-card">
          <p class="text-sm text-gray-600">Conversations</p>
          <p class="text-2xl font-bold text-blue-600">{{ todayStats.conversation || 0 }}</p>
        </div>
      </div>
    </div>

    <!-- Weekly Graph -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Weekly Activity</h2>
      <div class="h-80">
        <ActivityChart 
          v-if="weeklyChartData.labels.length > 0" 
          :data="weeklyChartData" 
          type="line" 
        />
        <div v-else class="flex items-center justify-center h-full text-gray-500">
          Loading chart data...
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        <router-link 
          to="/business-types"
          class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200"
        >
          <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-700">Business Types</span>
        </router-link>

        <router-link 
          to="/categories"
          class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200"
        >
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-700">Categories</span>
        </router-link>

        <router-link 
          to="/locations"
          class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200"
        >
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-700">Locations</span>
        </router-link>

        <router-link 
          to="/product-units"
          class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200"
        >
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-700">Product Units</span>
        </router-link>

        <router-link 
          to="/lead-credit-packages"
          class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200"
        >
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-700">Lead Packages</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useDashboardStore } from '../stores/dashboard'
import ActivityChart from '../components/ActivityChart.vue'

const dashboardStore = useDashboardStore()

// SMS Balance data
const smsBalance = ref(0)
const smsBalanceLoading = ref(false)

// Dashboard data
const dashboardSummary = computed(() => dashboardStore.summary)
const todayStats = computed(() => dashboardStore.todayStats)
const weeklyGraph = computed(() => dashboardStore.weeklyGraph)

// Formatted SMS balance with 2 decimal places
const formattedSmsBalance = computed(() => {
  return smsBalance.value.toFixed(2)
})

// Prepare chart data for ActivityChart component
const weeklyChartData = computed(() => {
  if (!weeklyGraph.value.data) {
    return {
      labels: [],
      datasets: []
    }
  }

  return {
    labels: weeklyGraph.value.data.labels || [],
    datasets: [
      {
        label: 'Users',
        backgroundColor: 'rgba(99, 102, 241, 0.2)',
        borderColor: 'rgba(99, 102, 241, 1)',
        borderWidth: 2,
        data: weeklyGraph.value.data.user || [],
        tension: 0.4,
        fill: true
      },
      {
        label: 'Businesses',
        backgroundColor: 'rgba(139, 92, 246, 0.2)',
        borderColor: 'rgba(139, 92, 246, 1)',
        borderWidth: 2,
        data: weeklyGraph.value.data.business || [],
        tension: 0.4,
        fill: true
      },
      {
        label: 'Products',
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 2,
        data: weeklyGraph.value.data.product || [],
        tension: 0.4,
        fill: true
      },
      {
        label: 'Leads',
        backgroundColor: 'rgba(245, 158, 11, 0.2)',
        borderColor: 'rgba(245, 158, 11, 1)',
        borderWidth: 2,
        data: weeklyGraph.value.data.lead || [],
        tension: 0.4,
        fill: true
      },
      {
        label: 'Conversations',
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        data: weeklyGraph.value.data.conversation || [],
        tension: 0.4,
        fill: true
      }
    ]
  }
})

const loadStats = async () => {
  try {
    await dashboardStore.fetchAllDashboardData()
  } catch (error) {
    console.error('Error loading stats:', error)
  }
}

const loadSmsBalance = async () => {
  smsBalanceLoading.value = true
  try {
    const response = await fetch('https://bulksmsbd.net/api/getBalanceApi?api_key=HNLjU0stwIAXijmqBJkx')
    const data = await response.json()
    if (data.response_code === 202 && typeof data.balance !== 'undefined') {
      smsBalance.value = parseFloat(data.balance)
    }
  } catch (error) {
    console.error('Error loading SMS balance:', error)
    smsBalance.value = 0
  } finally {
    smsBalanceLoading.value = false
  }
}

onMounted(() => {
  loadStats()
  loadSmsBalance()
})
</script>