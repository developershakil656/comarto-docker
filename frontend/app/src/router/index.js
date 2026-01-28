import { createRouter, createWebHistory } from 'vue-router'
import store from '../store/index.js'

// Layouts
import HomeLayout from '../layouts/HomeLayout.vue'
import UserProfileLayout from '../layouts/UserProfileLayout.vue'

// Views
import HomeView from '../views/HomeView.vue'
import SearchView from '../views/SearchView.vue'
import ProductDetailsView from '../views/ProductDetailsView.vue'
import BusinessView from '../views/BusinessProfileView.vue'
import FreeListingView from '../views/FreeListingView.vue'
import RegisterBusinessView from '../views/RegisterBusinessView.vue'
import CategoryDetailView from '../views/CategoryDetailView.vue'
import AllCategoriesView from '../views/AllCategoriesView.vue'

// User Views
import AccountDetails from '../views/user/AccountDetails.vue'
import Following from '../views/user/Following.vue'
import Favourites from '../views/user/Favourites.vue'
import Reviews from '../views/user/Reviews.vue'
import IdentityVerify from '../views/user/IdentityVerify.vue'
import Inquiries from '../views/user/Inquiries.vue'
import ConversationsListView from '../views/ConversationsListView.vue'
import ConversationMessagesView from '../views/ConversationMessagesView.vue'

// Business Views
import BusinessDashboard from '../views/business/BusinessDashboard.vue'
import BusinessDetails from '../views/business/BusinessDetails.vue'
import BusinessReviews from '../views/business/BusinessReviews.vue'
import AddProduct from '../views/business/AddProduct.vue'
import BusinessProducts from '../views/business/BusinessProducts.vue'
import EditProduct from '../views/business/EditProduct.vue'
import LeadsView from '../views/business/LeadsView.vue'
import BuyLeadCredits from '../views/business/BuyLeadCredits.vue'

// Legal/Static Views
import MakeReviewView from '../views/MakeReviewView.vue'
import PrivacyPolicyView from '../views/PrivacyPolicyView.vue'
import TermsConditionsView from '../views/TermsConditionsView.vue'
import RefundPolicyView from '../views/RefundPolicyView.vue'
import DisclaimerView from '../views/DisclaimerView.vue'

const routes = [
  // --- GROUP 1: Routes using HomeLayout (Header/Nav included) ---
  {
    path: '/',
    component: HomeLayout,
    children: [
      { path: '', name: 'home', component: HomeView },
      {
        path: 'search/:location(all-bangladesh|[^/]+)/:keyword?',
        name: 'search',
        component: SearchView,
        props: true
      },
      { path: 'categories', name: 'all-categories', component: AllCategoriesView },
      { path: 'category/:categorySlug', name: 'category-detail', component: CategoryDetailView },
      { path: 'product/:slug', name: 'product-detail', component: ProductDetailsView, props: true },
      { 
        path: 'free/listing', 
        name: 'free-listing', 
        component: FreeListingView, 
        props: true, 
        meta: { requiresNoBusiness: true } 
      },
      {
        path: 'new/business/register',
        name: 'business-register',
        component: RegisterBusinessView,
        meta: { requiresAuth: true, requiresNoBusiness: true }
      },
      
      // User Profile Nested inside HomeLayout
      {
        path: 'user',
        component: UserProfileLayout,
        meta: { requiresAuth: true },
        children: [
          { path: 'account/details', name: 'account-details', component: AccountDetails },
          { path: 'following', name: 'following', component: Following },
          { path: 'favourites', name: 'favourites', component: Favourites },
          { path: 'reviews', name: 'reviews', component: Reviews },
          { path: 'identity/verify', name: 'identity-verify', component: IdentityVerify },
          { path: 'inquiries', name: 'inquiries', component: Inquiries },
        ]
      },

      // Messages inside HomeLayout
      { path: 'messages', name: 'conversations', component: ConversationsListView, meta: { requiresAuth: true } },
      { path: 'messages/:id', name: 'conversation-messages', component: ConversationMessagesView, meta: { requiresAuth: true } },

      // Legal Pages
      { path: 'privacy-policy', name: 'privacy-policy', component: PrivacyPolicyView },
      { path: 'terms-conditions', name: 'terms-conditions', component: TermsConditionsView },
      { path: 'refund-policy', name: 'refund-policy', component: RefundPolicyView },
      { path: 'disclaimer', name: 'disclaimer', component: DisclaimerView },

      // Business Profile & Reviews
      { path: ':businessSlug/review', name: 'make-review', component: MakeReviewView, props: true },
      { path: ':businessSlug', name: 'business', component: BusinessView, props: true },
    ]
  },

  // --- GROUP 2: Routes EXCLUDED from HomeLayout (Business Management) ---
  {
    path: '/my/business',
    meta: { requiresAuth: true, requiresMyBusiness: true },
    children: [
      { path: 'dashboard', name: 'business-dashboard', component: BusinessDashboard },
      { path: 'details', name: 'business-details', component: BusinessDetails },
      { path: 'reviews', name: 'business-review', component: BusinessReviews },
      { path: 'add/product', name: 'business-add-product', component: AddProduct },
      { path: 'products', name: 'business-products', component: BusinessProducts },
      { path: 'product/edit/:id', name: 'EditProduct', component: EditProduct },
      { path: 'leads', name: 'business-leads', component: LeadsView },
      { path: 'lead-credits/buy', name: 'business-buy-lead-credits', component: BuyLeadCredits },
    ]
  },

  // --- GROUP 3: Authentication Callbacks (No Layout) ---
  {
    path: '/user/auth/google/callback',
    name: 'google-callback',
    component: () => import('../views/GoogleCallbackView.vue')
  },
  
  // Catch-all route for 404 pages
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFoundView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  window.scrollTo(0, 0)
  
  const isAuthenticated = store.getters.isAuthenticated
  const hasBusiness = store.getters.user?.business

  if (to.meta.requiresAuth && !isAuthenticated) {
    localStorage.setItem('intendedRoute', to.fullPath)
    next({ name: 'home' })
    return
  }

  if (to.meta.requiresMyBusiness && !hasBusiness) {
    next({ name: 'free-listing' })
    return
  }
  
  if (to.meta.requiresNoBusiness && hasBusiness) {
    next({ name: 'business-dashboard' })
    return
  }
  
  next()
})

export default router