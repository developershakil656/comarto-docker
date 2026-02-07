<template>
  <div class="bg-gray-50 min-h-screen font-poppins">
    <div class="hidden md:block">
      <AdminDashboardHeader title="Leads" backUrl="/my/business/dashboard" />
    </div>

        <div class="container mx-auto mb-6">
          <!-- Credits Banner -->
          <section class="md:mx-12 mt-6">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg p-3 md:p-6 text-white">
              <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex-1">
                  <h2 class="text-xl font-bold mb-2">Lead Credits</h2>
                  <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-6">
                    <div class="flex items-center gap-2">
                      <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                      <span class="text-sm">Free: {{ leadCredits?.free_remaining || 0 }} remaining</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <div :class="[
                        'w-3 h-3 rounded-full shrink-0',
                        isCreditsExpired ? 'bg-red-400' : 'bg-yellow-400'
                      ]"></div>
                      <span class="text-sm">Paid: {{ leadCredits?.paid_credits || 0 }} available</span>
                    </div>
                    <div v-if="leadCredits?.expire_date" class="flex items-center gap-2">
                      <div :class="[
                        'w-3 h-3 rounded-full shrink-0',
                        isCreditsExpired ? 'bg-red-400' : 'bg-orange-400'
                      ]"></div>
                      <span class="text-sm">
                        {{ isCreditsExpired ? 'Expired' : 'Expires' }}: {{ leadCredits.expire_date }}
                      </span>
                    </div>
                  </div>
                </div>
                <router-link to="/my/business/lead-credits/buy" class="w-full md:w-auto text-center px-6 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-50 transition">
                  Buy Credits
                </router-link>
              </div>
              <div v-if="isCreditsExpired" class="mt-4 p-3 bg-red-500 bg-opacity-20 border border-red-300 rounded-lg">
                <p class="text-sm text-red-100">
                  ⚠️ Your paid credits have expired. Purchase new credits to continue viewing lead contacts.
                </p>
              </div>
            </div>
          </section>

          <!-- Tabs Navigation -->
          <section class="md:mx-12 mt-6">
            <div class="bg-white rounded-xl shadow border relative">
              <button v-if="showLeftArrow" @click="scrollTabs('left')" class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10">
                <ChevronLeftIcon class="h-6 w-6 text-gray-700" />
              </button>
              <div ref="tabContainer" @scroll="checkScroll" class="flex border-b overflow-x-auto scrollbar-hide">
                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                  'flex-1 px-3 md:px-6 py-3 md:py-4 text-sm md:text-base text-center font-medium transition-colors whitespace-nowrap min-w-max',
                  activeTab === tab.id
                    ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50'
                    : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
                ]">
                  {{ tab.label }}
                  <span v-if="tab.count" class="ml-1 md:ml-2 bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">
                    {{ tab.count }}
                  </span>
                </button>
              </div>
              <button v-if="showRightArrow" @click="scrollTabs('right')" class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10">
                <ChevronRightIcon class="h-6 w-6 text-gray-700" />
              </button>
            </div>
          </section>

          <!-- Filters and Search -->
          <section v-if="activeTab === 'all'" class="md:mx-12 mt-6">
            <!-- Clear Filters Button -->
            <div v-if="hasActiveFilters" class="flex justify-end mb-4">
              <button @click="clearFilters"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                Clear All Filters
              </button>
            </div>

            <div class="bg-white rounded-xl shadow border p-3 md:p-6">
              <div class="flex flex-col lg:flex-row gap-6">
                <!-- Search -->
                <div class="flex-1">
                  <label for="leads-search" class="block text-sm font-medium text-gray-700 mb-2">Search Leads</label>
                  <div class="relative">
                    <input v-model="searchKeyword" type="text" id="leads-search" name="leads-search"
                      placeholder="Enter product / service to search"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    <MagnifyingGlassIcon
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                  </div>
                </div>

                <!-- Location Filter -->
                <div class="w-full lg:w-64">
                  <label for="leads-location-search"
                    class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                  <div class="relative location-dropdown">
                    <input id="leads-location-search" name="leads-location-search"
                      :placeholder="selectedLocation || 'All Bangladesh'"
                      :value="locationSearch"
                      @input="locationSearch = $event.target.value; handleLocationInput()"
                      @focus="closeAllDropdowns(); showLocationDropdown = true" type="text"
                      class="w-full px-4 py-3 border placeholder-gray-800 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10" />
                    <!-- Clear location button -->
                    <button v-if="selectedLocation" @click="clearLocation"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 hover:text-gray-600 transition-colors"
                      title="Clear location">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                      </svg>
                    </button>
                    <div v-if="showLocationDropdown && locations.length > 0"
                      class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                      <div
                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-black border-b border-gray-100 last:border-b-0"
                        v-if="!locationSearch" @click="selectLocation('')">
                        All Bangladesh
                      </div>
                      <div v-for="location in locations" :key="location.id"
                        @click="selectLocation(setLocationName(location))"
                        class="px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors border-b border-gray-100 last:border-b-0">
                        {{ setLocationName(location) }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Category Filter -->
                <div class="w-full lg:w-64 category-dropdown">
                  <label for="leads-category-search"
                    class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
                  <div class="relative">
                    <input :value="categorySearch" id="leads-category-search" name="leads-category-search"
                      :placeholder="selectedCategories.length ? `${selectedCategories.length} selected` : 'Select Categories'"
                      @focus="closeAllDropdowns(); showCategoryDropdown = true" @input="categorySearch = $event.target.value; handleCategoryInput()" type="text"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    <div v-if="showCategoryDropdown"
                      class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                      <div class="p-2">
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                          <input type="checkbox" id="select-all-categories" name="select-all-categories"
                            :checked="selectedCategories.length === productCategories.length && productCategories.length > 0"
                            @change="selectAllCategories" class="mr-3 rounded" />
                          <span class="text-sm">All Categories</span>
                        </label>

                        <!-- No categories available -->
                        <div
                          v-if="!categorySearch.trim() && productCategories.length === 0 && businessCategories.length === 0"
                          class="p-4 text-center text-gray-500 text-sm">
                          No categories available
                        </div>

                        <!-- Categories List (filtered by search or separated by sections) -->
                        <div v-if="!categorySearch.trim()" class="border-t border-gray-200 pt-2 mt-2">
                          <!-- Your Categories Section -->
                          <div v-if="businessCategories.length > 0" class="mb-3">
                            <div class="text-xs font-medium text-gray-500 mb-2 px-2">Your Categories</div>
                            <div v-for="category in businessCategories" :key="category.id" class="p-2">
                              <label class="flex items-center hover:bg-blue-50 rounded cursor-pointer">
                                <input type="checkbox" :value="category.id" v-model="selectedCategories"
                                  class="mr-3 rounded" />
                                <span class="text-sm font-medium text-blue-700">{{ category.name }}</span>
                              </label>
                            </div>
                          </div>

                          <!-- Other Categories Section -->
                          <div
                            v-if="productCategories.filter(cat => !businessCategories.map(bc => bc.id).includes(cat.id)).length > 0">
                            <div class="text-xs font-medium text-gray-500 mb-2 px-2">Other Categories</div>
                            <div
                              v-for="category in productCategories.filter(cat => !businessCategories.map(bc => bc.id).includes(cat.id))"
                              :key="category.id" class="p-2">
                              <label class="flex items-center hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" :value="category.id" v-model="selectedCategories"
                                  class="mr-3 rounded" />
                                <span class="text-sm">{{ category.name }}</span>
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Search Results -->
                        <div v-else class="border-t border-gray-200 pt-2 mt-2">
                          <div class="text-xs font-medium text-gray-500 mb-2 px-2">Search Results</div>
                          <div v-if="productCategories.length > 0">
                            <div v-for="category in productCategories" :key="category.id" class="p-2">
                              <label class="flex items-center hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" :value="category.id" v-model="selectedCategories"
                                  class="mr-3 rounded" />
                                <span class="text-sm"
                                  :class="businessCategories.find(bc => bc.id === category.id) ? 'font-medium text-blue-700' : ''">
                                  {{ category.name }}
                                </span>
                              </label>
                            </div>
                          </div>
                          <div v-else class="p-4 text-center text-gray-500 text-sm">
                            No categories found for "{{ categorySearch }}"
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Sort Options -->
                <div class="w-full lg:w-48">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                  <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                    <button @click="sortBy = 'relevant'" :class="[
                      'flex-1 px-4 py-3 text-sm font-medium transition-colors',
                      sortBy === 'relevant'
                        ? 'bg-blue-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-50'
                    ]">
                      Relevant
                    </button>
                    <button @click="sortBy = 'recent'" :class="[
                      'flex-1 px-4 py-3 text-sm font-medium transition-colors',
                      sortBy === 'recent'
                        ? 'bg-blue-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-50'
                    ]">
                      Recent
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Leads List -->
          <section class="md:mx-12 mt-6">
            <!-- Loading State -->
            <div v-if="loading" class="bg-white rounded-xl shadow border p-3 md:p-6">
              <div class="grid gap-4 sm:gap-6">
                <div v-for="i in 3" :key="i" class="bg-gray-50 max-w-full overflow-hidden rounded-xl p-3 sm:p-4 md:p-6 border border-gray-200 animate-pulse">
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-3 sm:gap-4">
                      <div class="flex items-start gap-2 sm:gap-3 flex-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-lg shrink-0"></div>
                        <div class="w-full min-w-0">
                          <div class="h-5 sm:h-6 bg-gray-200 rounded w-3/4 sm:w-48 mb-1 sm:mb-2"></div>
                          <div class="flex flex-wrap items-center gap-1 sm:gap-2">
                            <div class="h-3 sm:h-4 bg-gray-200 rounded w-2/3 sm:w-32"></div>
                            <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-24"></div>
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center gap-1 sm:gap-2 self-start sm:self-center shrink-0">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 bg-gray-200 rounded"></div>
                        <div class="w-6 h-6 sm:w-7 sm:h-7 bg-gray-200 rounded"></div>
                      </div>
                    </div>
                    <div class="flex flex-col max-w-full md:flex-row justify-between items-start md:items-center mt-3 sm:mt-4 gap-3 sm:gap-4">
                      <div class="flex-1 min-w-0">
                        <div class="mb-3 sm:mb-4">
                          <div class="h-3 sm:h-4 bg-gray-200 rounded w-full mb-2 sm:mb-3"></div>
                          <div class="h-3 sm:h-4 bg-gray-200 rounded w-3/4 mb-2 sm:mb-3"></div>
                          <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-2 sm:gap-x-4 gap-y-1 sm:gap-y-2">
                            <div class="h-3 sm:h-4 bg-gray-200 rounded w-24 sm:w-32"></div>
                          </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
                          <div class="h-3 sm:h-4 bg-gray-200 rounded w-16 sm:w-20"></div>
                          <div class="flex items-center gap-2 sm:gap-3">
                            <div class="h-3 sm:h-4 bg-gray-200 rounded w-12 sm:w-16"></div>
                            <div class="h-3 sm:h-4 bg-gray-200 rounded w-12 sm:w-16"></div>
                            <div class="h-3 sm:h-4 bg-gray-200 rounded w-12 sm:w-16"></div>
                          </div>
                        </div>
                      </div>
                      <div class="flex flex-col gap-1 sm:gap-2 items-start md:items-end w-full md:w-auto">
                        <div class="h-8 sm:h-10 bg-gray-200 rounded w-full md:w-32"></div>
                        <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-24"></div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <!-- Empty State -->
            <div v-else-if="filteredLeads.length === 0" class="bg-white rounded-xl shadow border p-8 text-center">
                <InboxIcon class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">No leads found</h3>
                <p class="text-gray-600">Try adjusting your filters or search criteria</p>
              </div>

            <!-- Leads Grid -->
            <div v-else class="bg-white rounded-xl shadow border">
              <div class="grid gap-6 p-3 md:p-6">
                <!-- Total leads found -->
                <div class="text-sm text-gray-600">{{ leadsTotal }} leads found</div>
                <div v-for="lead in filteredLeads" :key="lead.id" :class="[
                  'bg-gray-50 rounded-xl p-4 md:p-6 border border-gray-200 transition-shadow',
                  lead.status === 'closed' ? 'opacity-60 cursor-not-allowed' : 'hover:shadow-md'
                ]">
                  <div class="flex flex-col gap-4">
                    <!-- Card Header -->
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                      <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                            <TagIcon class="w-6 h-6 text-blue-600" />
                          </div>
                          <div>
                            <h3 class="md:text-lg font-semibold text-gray-900 capitalize">
                              {{ lead.category?.name || 'General Inquiry' }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-500">
                              <MapPinIcon class="w-4 h-4" />
                              <span>{{ setLocationName(lead.location) }}</span>
                              <span class="mx-2 md:block hidden">•</span>
                              <div class="flex">
                                <ClockIcon class="w-4 h-4 mr-1" />
                                <span>{{ getTimeAgo(lead.created_at) }}</span>
                              </div>
                              <span v-if="lead.status === 'closed'"
                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                Closed
                              </span>
                            </div>
                          </div>
                      </div>
                      <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                        <div :class="[
                            'px-1 transition-colors',
                            isUnlockLead(lead.id)
                              ? ' text-green-600'
                              : ' text-gray-700'
                          ]">
                            <LockClosedIcon v-if="!isUnlockLead(lead.id)" class="w-7 h-7" />
                            <LockOpenIcon v-else class="w-7 h-7" />
                          </div>

                        <button @click="toggleFavorite(lead)" :class="[
                            'px-1 transition-colors',
                            isLeadFavorite(lead.id)
                              ? ' text-primary/85'
                              : ' text-gray-700'
                          ]">
                            <BookmarkIcon :class="['w-7 h-7', isLeadFavorite(lead.id) ? 'fill-current' : '']" />
                          </button>
                      </div>
                    </div>

                    <!-- Card Body & Actions -->
                    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
                      <div class="flex-1">
                          <div class="mb-4">
                            <p class="text-gray-700 mb-3">{{ lead.description }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 text-sm">
                              <div v-if="lead.quantity">
                                <span class="font-medium text-gray-600">Quantity:</span>
                                <span class="ml-2">{{ lead.quantity }} {{ lead.unit_name }}</span>
                              </div>
                            </div>
                          </div>

                          <!-- Contact Availability -->
                          <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-600 hidden md:block">Available:</span>
                            <div class="flex items-center gap-3">
                              <div class="flex items-center gap-1"
                                :class="lead.user?.number ? 'text-primary font-medium' : 'text-gray-600'">
                                <PhoneIcon class="w-4" />
                                <span class="text-xs">Mobile</span>
                              </div>
                              <div class="flex items-center gap-1"
                                :class="lead.user?.email ? 'text-primary font-medium' : 'text-gray-600'">
                                <EnvelopeIcon class="w-4" />
                                <span class="text-xs">Email</span>
                              </div>
                              <div class="flex items-center gap-1"
                                :class="lead.user?.address ? 'text-primary font-medium' : 'text-gray-600'">
                                <MapPinIcon class="w-4" />
                                <span class="text-xs">Address</span>
                              </div>

                              <VerifiedBadge :accountVerified="lead.user.account_verification" />
                            </div>
                        </div>
                      </div>
                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 items-start md:items-end w-full md:w-auto">
                          <!-- <button @click="toggleFavorite(lead)" :class="[
                        'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                        isLeadFavorite(lead.id)
                          ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                      ]">
                        <BookmarkIcon :class="['w-4 h-4', isLeadFavorite(lead.id) ? 'fill-current' : '']" />
                      </button> -->
                          <div>
                            <button @click="viewContact(lead)" :disabled="lead.status === 'closed'" class="w-full md:w-auto"
                              :title="lead.status === 'closed' ? 'Lead is closed' : 'View contact'" :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                lead.status === 'closed' ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700'
                              ]">
                              View Contact 
                            </button>
                            <h2 class="text-sm text-gray-600 mt-2 flex gap-1">
                              <BriefcaseIcon class="w-4 h-4" />{{ lead.proposal }} Proposal
                            </h2>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
                <div ref="loadMoreRef" class="h-6"></div>
                <div v-if="leadsLoadingMore" class="text-center text-gray-500 py-2">Loading more...</div>
                <div v-else-if="!leadsCanLoadMore && filteredLeads.length"
                  class="text-center text-gray-400 py-2 text-sm">No more results</div>
              </div>
            </div>
          </section>
        </div>

        <BusinessMobileBottomNavigation/>

    <!-- Contact Details Modal -->
    <ContactDetailsModal :open="showContactModal" :contact-data="selectedContactData" :lead-data="selectedLeadData"
      @close="closeContactModal" />

    <!-- Buy Credits Modal -->
    <BuyCreditsModal :open="showBuyCreditsModal" :lead-data="selectedLeadForCredits"
      @close="showBuyCreditsModal = false" @credits-purchased="refreshCredits"
      @view-contact="handleViewContactFromModal" />

    <!-- Error Modal -->
    <ErrorModal :open="showErrorModal" :message="errorMessage" @close="showErrorModal = false" />
  </div>
</template>

<script>
import {
  ChevronLeftIcon,
  MagnifyingGlassIcon,
  ChevronRightIcon,
  ChevronDownIcon,
  TagIcon,
  MapPinIcon,
  ClockIcon,
  PhoneIcon,
  EnvelopeIcon,
  BookmarkIcon,
  InboxIcon,
  BriefcaseIcon,
  LockClosedIcon,
  LockOpenIcon
} from '@heroicons/vue/24/outline';
import Swal from 'sweetalert2';
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue';
import ContactDetailsModal from './modals/ContactDetailsModal.vue';
import BuyCreditsModal from './modals/BuyCreditsModal.vue';
import ErrorModal from './modals/ErrorModal.vue';
import SkeletonLoader from '@/components/SkeletonLoader.vue';
import VerifiedBadge from '@/components/common/VerifiedBadge.vue';
import BusinessMobileBottomNavigation from '@/components/business/BusinessMobileBottomNavigation.vue';


export default {
  name: 'LeadsView',
  components: {
    AdminDashboardHeader,
    ContactDetailsModal,
    BuyCreditsModal,
    ErrorModal,
    VerifiedBadge,
    MagnifyingGlassIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    TagIcon,
    MapPinIcon,
    ClockIcon,
    PhoneIcon,
    EnvelopeIcon,
    BookmarkIcon,
    InboxIcon,
    BriefcaseIcon,
    LockClosedIcon,
    LockOpenIcon,
    SkeletonLoader,
    BusinessMobileBottomNavigation
  },
  data() {
    return {
      activeTab: 'all',
      searchKeyword: '',
      selectedLocation: this.$store.getters.selectedLocation,
      selectedCategories: [],
      showCategoryDropdown: false,
      showLocationDropdown: false,
      locationSearch: this.$store.getters.selectedLocation,
      categorySearch: '',
      sortBy: 'relevant',
      loading: true,
      leads: [],
      showContactModal: false,
      showBuyCreditsModal: false,
      showErrorModal: false,
      selectedContactData: null,
      selectedLeadData: null,
      selectedLeadForCredits: null,
      errorMessage: '',
      searchTimeout: null,
      tabs: [
        { id: 'all', label: 'All Leads', count: 0 },
        { id: 'unlocked', label: 'Unlocked Leads', count: 0 },
        { id: 'saved', label: 'Saved Leads', count: 0 }
      ],
      showLeftArrow: false,
      showRightArrow: false,
      businessCategories: [], // Store business categories from API
      allCategories: [], // Store all available categories
      observer: null
    };
  },
  computed: {
    // selectedLocation() {
    //   return this.$store.getters.selectedLocation;
    // },
    filteredLeads() {
      // Return appropriate leads based on active tab
      switch (this.activeTab) {
        case 'unlocked':
          return this.storeUnlockedLeads;
        case 'saved':
          return this.storeFavoriteLeads;
        case 'all':
        default:
          return this.storeLeads;
      }
    },
    leadCredits() {
      return this.myBusiness?.lead_credits;
    },
    myBusiness() {
      return this.$store.getters.myBusiness;
    },
    isCreditsExpired() {
      if (!this.leadCredits?.expire_date) return false;
      const expireDate = new Date(this.leadCredits.expire_date);
      const now = new Date();
      return this.leadCredits.paid_credits > 0 && now > expireDate;
    },
    // Store getters for leads
    storeLeads() {
      return this.$store.getters.leads;
    },
    storeUnlockedLeads() {
      return this.$store.getters.unlockedLeads;
    },
    storeUnlockedLeadsLoading() {
      return this.$store.getters.unlockedLeadsLoading;
    },
    storeFavoriteLeads() {
      return this.$store.getters.favoriteLeads;
    },
    storeLeadsLoading() {
      return this.$store.getters.leadsLoading;
    },
    leadsMeta() {
      return this.$store.getters.leadsMeta;
    },
    leadsTotal() {
      if (this.activeTab === 'unlocked') {
        return this.unlockedLeadsMeta?.total || this.filteredLeads.length;
      }
      return this.leadsMeta?.total || this.filteredLeads.length;
    },
    leadsCanLoadMore() {
      if (this.activeTab === 'unlocked') {
        return this.$store.getters.unlockedLeadsCanLoadMore;
      }
      return this.$store.getters.leadsCanLoadMore;
    },
    leadsLoadingMore() {
      if (this.activeTab === 'unlocked') {
        return this.$store.getters.unlockedLeadsLoadingMore;
      }
      return this.$store.getters.leadsLoadingMore;
    },
    unlockedLeadsMeta() {
      return this.$store.getters.unlockedLeadsMeta;
    },
    locations() {
      return this.$store.getters.locations;
    },
    productCategories() {
      // If there's a search keyword, return the filtered results from API
      if (this.categorySearch.trim()) {
        return this.allCategories;
      }

      // Otherwise, show business categories first, then other categories (no duplicates)
      const businessCategoryIds = this.businessCategories.map(cat => cat.id);
      const otherCategories = this.allCategories.filter(cat => !businessCategoryIds.includes(cat.id));

      return [
        ...this.businessCategories, // Your categories first
        ...otherCategories // Other categories after (no duplicates)
      ];
    },
    hasActiveFilters() {
      return this.searchKeyword.trim() !== '' ||
        this.selectedLocation.trim() !== '' ||
        this.selectedCategories.length > 0;
    }
  },
  methods: {
    selectAllCategories() {
      // If all categories are already selected, deselect all
      if (this.selectedCategories.length === this.productCategories.length && this.productCategories.length > 0) {
        this.selectedCategories = [];
      } else {
        // Otherwise, select all available category IDs from both business categories and other categories
        const allCategoryIds = this.productCategories.map(category => category.id);
        this.selectedCategories = allCategoryIds;
      }
    },
    closeAllDropdowns() {
      this.showLocationDropdown = false;
      this.showCategoryDropdown = false;
      // Don't clear categorySearch here to keep search results visible
      // Don't clear locationSearch here to keep selected location visible
    },
    handleLocationInput() {
      // Clear selected location if user starts typing
      // if (this.selectedLocation !== '' && this.locationSearch !== this.selectedLocation) {
      //   this.selectedLocation = '';
      // }

      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.$store.dispatch('fetchLocations', this.locationSearch ? this.locationSearch.replace(/, District$/, '') : '');
      }, 500);
    },
    handleCategoryInput() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(async () => {
        try {
          if (this.categorySearch.trim()) {
            // Search for categories with the keyword
            await this.$store.dispatch('searchProductCategories', this.categorySearch);
            // Update allCategories with search results
            this.allCategories = this.$store.getters.productCategories;
          } else {
            // If search is cleared, fetch all categories
            await this.$store.dispatch('searchProductCategories');
            this.allCategories = this.$store.getters.productCategories;
          }
        } catch (error) {
          console.error('Error searching categories:', error);
        }
      }, 300);
    },
    selectLocation(location) {
      // this.$store.dispatch('setSelectedLocation', location);
      this.selectedLocation = location;
      this.locationSearch = location;
      this.showLocationDropdown = false;
    },
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`
      } else {
        return `${location.district_name}, District`
      }
    },
    isLeadFavorite(leadId) {
      // Check if the lead is in the favorite leads array
      return this.storeFavoriteLeads.some(lead => lead.id === leadId);
    },
    isUnlockLead(leadId) {
      // Check if the lead is in the unlocked leads array
      return this.storeUnlockedLeads.some(lead => lead.id === leadId);
    },
    async toggleFavorite(lead) {
      try {
        // Use single toggle function - backend handles the toggling
        await this.$store.dispatch('toggleLeadFavorite', lead.id);
        // Refresh favorite leads to update the UI
        await this.$store.dispatch('fetchFavoriteLeads');

        // this.tabs[2].count = this.storeFavoriteLeads.length;
      } catch (error) {
        console.error('Error toggling favorite:', error);
        // Show error message to user
        this.errorMessage = 'Failed to update favorite status. Please try again.';
        this.showErrorModal = true;
      }
    },
    async viewContact(lead) {
      try {
        // Prevent viewing contact for closed leads
        if (lead?.status === 'closed') {
          return;
        }
        // Check if lead is already unlocked
        const isUnlocked = this.isUnlockLead(lead.id);

        // Check if user has enough credits (considering expiry)
        const hasValidCredits = this.leadCredits?.free_remaining > 0 ||
          (this.leadCredits?.paid_credits > 0 && !this.isCreditsExpired);

        if (!hasValidCredits && !isUnlocked) {
          // Set the lead data for the credits modal
          this.selectedLeadForCredits = lead;
          this.showBuyCreditsModal = true;
          return;
        }

        // Show confirmation alert for using 1 Lead Credit
        if (!isUnlocked) {
          const result = await Swal.fire({
            title: 'Use Lead Credit?',
            text: 'This will use 1 Lead Credit. Do you want to continue?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, use credit!',
            cancelButtonText: 'Cancel'
          });

          if (!result.isConfirmed) {
            return;
          }
        }

        // Always call unlockLeadContact (backend handles filtering)
        try {
          const response = await this.$store.dispatch('unlockLeadContact', lead.id);

          if (response.status) {
            this.selectedContactData = response.data;
            this.selectedLeadData = lead; // Store the original lead data
            this.showContactModal = true;

            if (!isUnlocked) {
              // Only fetch unlocked leads if the lead was not already unlocked
              await this.$store.dispatch('fetchUnlockedLeads');
              // Refresh business data to get updated credits only when credits are actually used
              await this.$store.dispatch('fetchMyBusiness');
            }
          } else {
            this.errorMessage = response.message;
            this.showErrorModal = true;
          }
        } catch (error) {
          console.error('Error unlocking contact:', error);
          this.errorMessage = 'Failed to unlock contact details. Please try again.';
          this.showErrorModal = true;
        }
      } catch (error) {
        console.error('Error viewing contact:', error);
        this.errorMessage = 'Failed to unlock contact details. Please try again.';
        this.showErrorModal = true;
      }
    },
    async loadLeads() {
      this.loading = true;
      try {
        let response;
        switch (this.activeTab) {
          case 'all':
            const filteredLocation = this.selectedLocation ? this.selectedLocation.replace(/, District$/, '') : null;
            response = await this.$store.dispatch('fetchLeads', {
              category_ids: this.selectedCategories.join(','),
              location: filteredLocation || '',
              keyword: this.searchKeyword,
              sort_by: this.sortBy
            });
            break;
          case 'unlocked':
            response = await this.$store.dispatch('fetchUnlockedLeads');
            break;
          case 'saved':
            response = await this.$store.dispatch('fetchFavoriteLeads');
            break;
        }

        if (response && response.status) {
          // Update local leads data to match the current tab
          this.updateLeadsForCurrentTab();
          // this.tabs[0].count = this.leads.length; // Update all leads count
        } else {
          // If no response or failed, set empty array
          this.leads = [];
          // this.tabs[0].count = 0;
        }
      } catch (error) {
        console.error('Error loading leads:', error);
        // Set empty array when API fails
        this.leads = [];
        // this.tabs[0].count = 0;
      } finally {
        this.loading = false;
        this.$nextTick(() => this.setupInfiniteScroll());
      }
    },
    updateLeadsForCurrentTab() {
      switch (this.activeTab) {
        case 'unlocked':
          this.leads = this.storeUnlockedLeads;
          break;
        case 'saved':
          this.leads = this.storeFavoriteLeads;
          break;
        case 'all':
        default:
          this.leads = this.storeLeads;
          break;
      }
    },
    setupInfiniteScroll() {
      if (this.observer) this.observer.disconnect();
      const options = { root: null, rootMargin: '200px', threshold: 0 };
      this.observer = new IntersectionObserver(async (entries) => {
        const entry = entries[0];
        if (entry && entry.isIntersecting && this.leadsCanLoadMore && !this.leadsLoadingMore && !this.storeLeadsLoading && !this.storeUnlockedLeadsLoading) {
          if (this.activeTab === 'unlocked') {
            await this.$store.dispatch('loadMoreUnlockedLeads');
          } else {
            await this.$store.dispatch('loadMoreLeads');
          }
          // Update local leads data to match the current tab
          this.updateLeadsForCurrentTab();
        }
      }, options);
      if (this.$refs.loadMoreRef) this.observer.observe(this.$refs.loadMoreRef);
    },
    async refreshCredits() {
      try {
        // Only refresh if credits were actually used (when credits are purchased)
        // This method is called from BuyCreditsModal when credits are purchased
        await this.$store.dispatch('fetchMyBusiness');
      } catch (error) {
        console.error('Error refreshing credits:', error);
      }
    },
    closeContactModal() {
      this.showContactModal = false;
      this.selectedContactData = null;
      this.selectedLeadData = null;
    },
    checkScroll(event) {
      const container = this.$refs.tabContainer;
      if (container) {
        const { scrollLeft, scrollWidth, clientWidth } = container;
        const buffer = 2;
        this.showLeftArrow = scrollLeft > buffer;
        this.showRightArrow = scrollLeft < scrollWidth - clientWidth - buffer;
      }
    },
    scrollTabs(direction) {
      const container = this.$refs.tabContainer;
      if (container) {
        // Scroll by a percentage of the container's width for a smoother experience
        const scrollAmount = container.clientWidth * 0.7;
        container.scrollBy({
          left: direction === 'left' ? -scrollAmount : scrollAmount,
          behavior: 'smooth'
        });
      }
    },
    checkInitialScroll() {
      this.$nextTick(() => {
        this.checkScroll();
      });
    },

    handleClickOutside(event) {
      // Close dropdowns if clicking outside
      if (this.showCategoryDropdown && !event.target.closest('.category-dropdown')) {
        this.showCategoryDropdown = false;
        // Clear category search when dropdown is closed
        this.categorySearch = '';
        // Refresh categories to show all categories again
        this.fetchAllCategories();
      }
      if (this.showLocationDropdown && !event.target.closest('.location-dropdown')) {
        this.showLocationDropdown = false;
        // Don't clear locationSearch here to keep selected location visible
      }
    },

    getTimeAgo(dateString) {
      if (!dateString) return 'Recently';

      const now = new Date();
      const date = new Date(dateString);
      const diffInMs = now - date;
      const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
      const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
      const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

      if (diffInMinutes < 1) return 'Just now';
      if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
      if (diffInHours < 24) return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
      if (diffInDays < 7) return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
      return date.toLocaleDateString();
    },
    clearLocation() {
      this.selectedLocation = '';
      this.locationSearch = '';
      this.loadLeads(); // Reload leads without location filter
    },
    clearFilters() {
      this.searchKeyword = '';
      this.selectedLocation = '';
      this.selectedCategories = [];
      this.locationSearch = '';
      this.categorySearch = '';
      this.sortBy = 'relevant';
      this.loadLeads(); // Reload leads with default filters
    },

    async fetchBusinessCategories() {
      try {
        const response = await this.$store.dispatch('fetchBusinessCategories');
        if (response.status) {
          this.businessCategories = response.data;
          // Set selected categories to business categories
          this.selectedCategories = this.businessCategories.map(cat => cat.id);
        }
        return response
      } catch (error) {
        console.error('Error fetching business categories:', error);
      }
    },

    async fetchAllCategories() {
      try {
        // Fetch all available categories from store
        await this.$store.dispatch('searchProductCategories');
        this.allCategories = this.$store.getters.productCategories;
      } catch (error) {
        console.error('Error fetching all categories:', error);
      }
    },

    async handleViewContactFromModal(data) {
      // Handle the view-contact event from BuyCreditsModal
      this.selectedLeadData = data.leadData;
      this.selectedContactData = data.contactData;
      this.showContactModal = true;
      // Only fetch unlocked leads if the lead was not already unlocked
      await this.$store.dispatch('fetchUnlockedLeads');
    }
  },
  async mounted() {
    // First fetch business categories and set them as selected
    const response = await this.fetchBusinessCategories();


    // Then fetch all categories and other data
    try {
      await Promise.all([
        this.fetchAllCategories(),
        this.$store.dispatch('fetchFavoriteLeads'),
        this.$store.dispatch('fetchUnlockedLeads'),
        this.$store.dispatch('fetchLocations')
      ]);
    } catch (error) {
      console.error('Error loading leads data:', error);
    }

    // Load leads after categories are set
    if (!response.data.length) {
      this.loadLeads();
    }

    // Add click outside handler for category dropdown
    document.addEventListener('click', this.handleClickOutside);

    // Initialize scroll arrow visibility
    this.$nextTick(() => {
      this.checkInitialScroll();
      window.addEventListener('resize', this.checkScroll);
    });
  },

  beforeUnmount() {
    // Clean up event listener
    document.removeEventListener('click', this.handleClickOutside);
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
      window.removeEventListener('resize', this.checkScroll);
    }
  },
  watch: {
    activeTab() {
      // Update leads data when tab changes
      this.$nextTick(() => {
        this.checkInitialScroll();
      });
      this.updateLeadsForCurrentTab();
    },
    searchKeyword() {
      // Debounce search
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        if (this.activeTab === 'all') {
          this.loadLeads();
        }
      }, 300);
    },
    selectedLocation() {
      if (this.activeTab === 'all') {
        this.loadLeads();
      }
    },

    showLocationDropdown(val) {
      if (!val) {
        this.locationSearch = this.selectedLocation;
      }
    },
    selectedCategories() {
      if (this.activeTab === 'all') {
        this.loadLeads();
      }
    },
    sortBy() {
      if (this.activeTab === 'all') {
        this.loadLeads();
      }
    },
    storeLeadsLoading(newVal) {
      if (!newVal) this.$nextTick(() => this.setupInfiniteScroll());
    },
    storeUnlockedLeadsLoading(newVal) {
      if (!newVal) this.$nextTick(() => this.setupInfiniteScroll());
    },
    filteredLeads() {
      this.$nextTick(() => this.setupInfiniteScroll());
    },
    // Update local leads when store data changes
    storeFavoriteLeads() {
      if (this.activeTab === 'saved') {
        this.updateLeadsForCurrentTab();
      }
    },
    storeLeads() {
      if (this.activeTab === 'all') {
        this.updateLeadsForCurrentTab();
      }
    },
    // Reactive tab counts
    storeUnlockedLeads: {
      handler(newVal) {
        this.tabs[1].count = this.unlockedLeadsMeta?.total || newVal.length;
        if (this.activeTab === 'unlocked') {
          this.updateLeadsForCurrentTab();
        }
      },
      immediate: true
    },
    unlockedLeadsMeta: {
      handler(newVal) {
        this.tabs[1].count = newVal?.total || this.storeUnlockedLeads.length;
      },
      immediate: true
    },
    storeFavoriteLeads: {
      handler(newVal) {
        this.tabs[2].count = newVal.length;
        if (this.activeTab === 'saved') {
          this.updateLeadsForCurrentTab();
        }
      },
      immediate: true
    }
  }
};
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
