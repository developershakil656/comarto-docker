import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginPage from '../views/LoginPage.vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import DashboardHome from '../views/DashboardHome.vue'
import BusinessTypes from '../views/BusinessTypes.vue'
import Businesses from '../views/Businesses.vue'
import Categories from '../views/Categories.vue'
import Locations from '../views/Locations.vue'
import ProductUnits from '../views/ProductUnits.vue'
import Products from '../views/Products.vue'
import Users from '../views/Users.vue'
import Leads from '../views/Leads.vue'
import AdminProfile from '../views/AdminProfile.vue'
import LeadCreditPackages from '../views/LeadCreditPackages.vue'
import LeadCreditPurchases from '../views/LeadCreditPurchases.vue'
// Account Verification
import AccountVerifications from '../views/AccountVerifications.vue'
// Home Top Categories
import HomeTopCategories from '../views/HomeTopCategories.vue'
// Business Management
import AddBusiness from '../views/AddBusiness.vue'
import EditBusiness from '../views/EditBusiness.vue'
// Product Management
import AddProduct from '../views/AddProduct.vue'
import EditProduct from '../views/EditProduct.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: DashboardHome
      },
      {
        path: 'business-types',
        name: 'BusinessTypes',
        component: BusinessTypes
      },
      {
        path: 'categories',
        name: 'Categories',
        component: Categories
      },
      {
        path: 'locations',
        name: 'Locations',
        component: Locations
      },
      {
        path: 'product-units',
        name: 'ProductUnits',
        component: ProductUnits
      },
      {
        path: 'profile',
        name: 'Profile',
        component: AdminProfile
      },
      {
        path: 'lead-credit-packages',
        name: 'LeadCreditPackages',
        component: LeadCreditPackages
      },
      {
        path: 'lead-credit-purchases',
        name: 'LeadCreditPurchases',
        component: LeadCreditPurchases
      },
      {
        path: 'businesses',
        name: 'Businesses',
        component: Businesses
      },
      {
        path: 'users',
        name: 'Users',
        component: Users
      },
      {
        path: 'products',
        name: 'Products',
        component: Products
      },
      {
        path: 'products/create',
        name: 'AddProduct',
        component: AddProduct
      },
      {
        path: 'products/:id/edit',
        name: 'EditProduct',
        component: EditProduct,
        props: true
      },
      {
        path: 'leads',
        name: 'Leads',
        component: Leads
      },
      {
        path: 'account-verifications',
        name: 'AccountVerifications',
        component: AccountVerifications
      },
      {
        path: 'home-top-categories',
        name: 'HomeTopCategories',
        component: HomeTopCategories
      },
      {
        path: 'businesses/create',
        name: 'AddBusiness',
        component: AddBusiness
      },
      {
        path: 'businesses/:id/edit',
        name: 'EditBusiness',
        component: EditBusiness,
        props: true
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router