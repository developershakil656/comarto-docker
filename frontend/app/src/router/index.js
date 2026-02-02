import { createRouter, createWebHistory } from 'vue-router'
import store from '../store/index.js'

// Layouts - Keep commonly used layouts loaded
import HomeLayout from '../layouts/HomeLayout.vue'
import UserProfileLayout from '../layouts/UserProfileLayout.vue'

// Lazy load views to reduce initial bundle size
const HomeView = () => import('../views/HomeView.vue')
const SearchView = () => import('../views/SearchView.vue')
const ProductDetailsView = () => import('../views/ProductDetailsView.vue')
const BusinessView = () => import('../views/BusinessProfileView.vue')
const FreeListingView = () => import('../views/FreeListingView.vue')
const RegisterBusinessView = () => import('../views/RegisterBusinessView.vue')
const CategoryDetailView = () => import('../views/CategoryDetailView.vue')
const AllCategoriesView = () => import('../views/AllCategoriesView.vue')
const RandomProductsView = () => import('../views/RandomProductsView.vue')

// User Views
const AccountDetails = () => import('../views/user/AccountDetails.vue')
const Following = () => import('../views/user/Following.vue')
const Favourites = () => import('../views/user/Favourites.vue')
const Reviews = () => import('../views/user/Reviews.vue')
const IdentityVerify = () => import('../views/user/IdentityVerify.vue')
const Inquiries = () => import('../views/user/Inquiries.vue')
const ConversationsListView = () => import('../views/ConversationsListView.vue')
const ConversationMessagesView = () => import('../views/ConversationMessagesView.vue')

// Business Views
const BusinessDashboard = () => import('../views/business/BusinessDashboard.vue')
const BusinessDetails = () => import('../views/business/BusinessDetails.vue')
const BusinessReviews = () => import('../views/business/BusinessReviews.vue')
const AddProduct = () => import('../views/business/AddProduct.vue')
const BusinessProducts = () => import('../views/business/BusinessProducts.vue')
const EditProduct = () => import('../views/business/EditProduct.vue')
const LeadsView = () => import('../views/business/LeadsView.vue')
const BuyLeadCredits = () => import('../views/business/BuyLeadCredits.vue')

// Legal/Static Views
const MakeReviewView = () => import('../views/MakeReviewView.vue')
const PrivacyPolicyView = () => import('../views/PrivacyPolicyView.vue')
const TermsConditionsView = () => import('../views/TermsConditionsView.vue')
const DisclaimerView = () => import('../views/DisclaimerView.vue')

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
      { path: 'random/products', name: 'random-products', component: RandomProductsView },
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