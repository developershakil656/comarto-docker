<template>
  <div class="animate-pulse">
    <!-- Product Card Skeleton -->
    <div v-if="type === 'product-card'" class="group flex flex-col sm:flex-row bg-white rounded-xl border p-3 sm:p-4 gap-3 sm:gap-4 items-start">
      <!-- Image skeleton -->
      <div class="w-full sm:w-40 md:w-52 h-40 sm:h-40 md:h-52 rounded-lg bg-gray-200"></div>
      
      <!-- Content skeleton -->
      <div class="flex-1 flex flex-col gap-2 sm:gap-3 w-full">
        <!-- Title skeleton -->
        <div class="flex justify-between items-start">
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-3/4 sm:w-2/3"></div>
          <div class="h-5 sm:h-6 w-5 sm:w-6 bg-gray-200 rounded flex-shrink-0"></div>
        </div>
        
        <!-- Price skeleton -->
        <div class="h-4 sm:h-5 bg-gray-200 rounded w-1/3 sm:w-1/4"></div>
        
        <!-- Store name skeleton -->
        <div class="flex items-center gap-2">
          <div class="h-4 sm:h-5 w-4 sm:w-5 bg-gray-200 rounded flex-shrink-0"></div>
          <div class="h-4 sm:h-5 bg-gray-200 rounded w-1/2 sm:w-2/3"></div>
        </div>
        
        <!-- Rating skeleton -->
        <div class="flex items-center gap-2">
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-12 sm:w-16"></div>
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-16 sm:w-20"></div>
        </div>
      </div>
    </div>

    <!-- Business Card Skeleton -->
    <div v-else-if="type === 'business-card'" class="group flex flex-col sm:flex-row bg-white rounded-xl border p-3 sm:p-4 gap-3 sm:gap-4 items-start">
      <!-- Image skeleton -->
      <div class="w-full sm:w-40 md:w-52 h-40 sm:h-40 md:h-52 rounded-lg bg-gray-200"></div>
      
      <!-- Content skeleton -->
      <div class="flex-1 flex flex-col gap-2 sm:gap-3 w-full">
        <!-- Business name skeleton -->
        <div class="flex justify-between items-start">
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-2/3 sm:w-3/4"></div>
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-16 sm:w-20 flex-shrink-0"></div>
        </div>
        
        <!-- Location skeleton -->
        <div class="flex items-center gap-2">
          <div class="h-3 sm:h-4 w-3 sm:w-4 bg-gray-200 rounded flex-shrink-0"></div>
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/3 sm:w-2/5"></div>
        </div>
        
        <!-- Tags skeleton -->
        <div class="flex gap-1 sm:gap-2 flex-wrap">
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-16 sm:w-20"></div>
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-12 sm:w-16"></div>
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-14 sm:w-18"></div>
        </div>
      </div>
    </div>

    <!-- Product Details Skeleton -->
    <div v-else-if="type === 'product-details'" class="container mx-auto my-3 sm:my-5 px-2 sm:px-4">
      <div class="flex flex-col lg:flex-row gap-3 sm:gap-4">
        <!-- Left Column -->
        <div class="lg:w-9/12 flex-col border border-gray-300 rounded-lg shadow-md p-1">
          <div class="flex gap-2 flex-col lg:flex-row">
            <!-- Image skeleton -->
            <div class="bg-white p-2 sm:p-3 lg:w-5/12">
              <div class="w-full h-64 sm:h-80 lg:h-96 bg-gray-200 rounded-lg"></div>
              <div class="flex gap-1 sm:gap-2 justify-center mt-2 overflow-x-auto">
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gray-200 rounded-lg flex-shrink-0"></div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gray-200 rounded-lg flex-shrink-0"></div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gray-200 rounded-lg flex-shrink-0"></div>
              </div>
            </div>

            <!-- Product info skeleton -->
            <div class="bg-white p-2 sm:p-3 lg:w-7/12">
              <!-- Title skeleton -->
              <div class="h-6 sm:h-7 bg-gray-200 rounded w-3/4 sm:w-2/3 mb-3 sm:mb-4"></div>
              
              <!-- Price skeleton -->
              <div class="h-5 sm:h-6 bg-gray-200 rounded w-1/3 sm:w-1/4 mb-3 sm:mb-4"></div>
              
              <!-- Quantity input skeleton -->
              <div class="flex gap-1 sm:gap-2 mb-4 sm:mb-6 flex-wrap">
                <div class="h-8 sm:h-10 bg-gray-200 rounded w-24 sm:w-32"></div>
                <div class="h-8 sm:h-10 bg-gray-200 rounded w-16 sm:w-20"></div>
                <div class="h-8 sm:h-10 bg-gray-200 rounded w-20 sm:w-24"></div>
              </div>
              
              <!-- Specification skeleton -->
              <div class="space-y-2">
                <div class="h-5 sm:h-6 bg-gray-200 rounded w-1/4 sm:w-1/3"></div>
                <div class="border rounded-md p-2 space-y-2">
                  <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                    <div class="h-6 sm:h-8 bg-gray-200 rounded w-full sm:w-1/3"></div>
                    <div class="h-6 sm:h-8 bg-gray-200 rounded w-full sm:w-2/3"></div>
                  </div>
                  <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                    <div class="h-6 sm:h-8 bg-gray-200 rounded w-full sm:w-1/4"></div>
                    <div class="h-6 sm:h-8 bg-gray-200 rounded w-full sm:w-3/4"></div>
                  </div>
                  <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                    <div class="h-6 sm:h-8 bg-gray-200 rounded w-full sm:w-1/3"></div>
                    <div class="h-6 sm:h-8 bg-gray-200 rounded w-full sm:w-2/3"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Description skeleton -->
          <div class="bg-white p-2 sm:p-3 mt-2 sm:mt-3">
            <div class="flex justify-between items-center mb-2">
              <div class="h-5 sm:h-6 bg-gray-200 rounded w-1/4 sm:w-1/3"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-16 sm:w-20"></div>
            </div>
            <div class="space-y-1 sm:space-y-2">
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-full"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-5/6"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-4/5"></div>
            </div>
          </div>
        </div>

        <!-- Right Column skeleton -->
        <div class="lg:w-3/12">
          <SkeletonLoader type="seller-information" />
        </div>
      </div>
    </div>

    <!-- Reviews Skeleton -->
    <div v-else-if="type === 'reviews'" class="space-y-4 sm:space-y-6">
      <div v-for="i in count" :key="i" class="border-b pb-4 sm:pb-6">
        <!-- Review header skeleton -->
        <div class="flex items-center mb-2">
          <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
          <div class="flex-1 min-w-0">
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-24 mb-1"></div>
            <div class="h-2 sm:h-3 bg-gray-200 rounded w-16 sm:w-20"></div>
          </div>
        </div>
        
        <!-- Rating skeleton -->
        <div class="h-4 sm:h-5 bg-gray-200 rounded w-20 sm:w-24 mb-2"></div>
        
        <!-- Message skeleton -->
        <div class="space-y-1 sm:space-y-2 mb-2 sm:mb-3">
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-full"></div>
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-3/4 sm:w-2/3"></div>
        </div>
        
        <!-- Reply skeleton -->
        <div class="bg-gray-50 rounded p-2 sm:p-3 mt-2 sm:mt-3">
          <div class="flex items-start">
            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-200 rounded-full mr-2 flex-shrink-0"></div>
            <div class="flex-1 min-w-0">
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-24 sm:w-32 mb-1"></div>
              <div class="h-2 sm:h-3 bg-gray-200 rounded w-16 sm:w-20 mb-1"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-full"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Related Products Skeleton -->
    <div v-else-if="type === 'related-products'" class="container mx-auto my-6 sm:my-10 px-2 sm:px-4">
      <div class="mx-1 sm:mx-2">
        <div class="flex justify-between items-center mb-3 sm:mb-4">
          <div class="h-6 sm:h-8 bg-gray-200 rounded w-32 sm:w-48"></div>
          <div class="flex space-x-1 sm:space-x-2">
            <div class="h-6 sm:h-8 w-6 sm:w-8 bg-gray-200 rounded"></div>
            <div class="h-6 sm:h-8 w-6 sm:w-8 bg-gray-200 rounded"></div>
          </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4">
          <div v-for="i in count" :key="i" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="w-full h-32 sm:h-40 lg:h-48 bg-gray-200"></div>
            <div class="p-2 sm:p-4 space-y-2 sm:space-y-3">
              <div class="h-4 sm:h-5 bg-gray-200 rounded w-3/4"></div>
              <div class="h-4 sm:h-6 bg-gray-200 rounded w-1/2"></div>
              <div class="h-6 sm:h-8 bg-gray-200 rounded w-full"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Related Categories Skeleton -->
    <div v-else-if="type === 'related-categories'" class="container mx-auto my-6 sm:my-10 px-2 sm:px-4">
      <div class="mx-1 sm:mx-2">
        <div class="flex justify-between items-center mb-3 sm:mb-4">
          <div class="h-6 sm:h-8 bg-gray-200 rounded w-32 sm:w-48"></div>
          <div class="flex space-x-1 sm:space-x-2">
            <div class="h-6 sm:h-8 w-6 sm:w-8 bg-gray-200 rounded"></div>
            <div class="h-6 sm:h-8 w-6 sm:w-8 bg-gray-200 rounded"></div>
          </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4">
          <div v-for="i in count" :key="i" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="w-full h-32 sm:h-40 lg:h-48 bg-gray-200"></div>
            <div class="p-2 sm:p-4 space-y-2 sm:space-y-3 mb-2 sm:mb-4">
              <div class="h-4 sm:h-5 bg-gray-200 rounded w-3/4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Seller Information Skeleton -->
    <div v-else-if="type === 'seller-information'" class="bg-white rounded-lg shadow-md p-3 sm:p-4">
      <!-- Header -->
      <div class="h-5 sm:h-6 bg-gray-200 rounded w-24 sm:w-32 mb-3 sm:mb-4"></div>
      
      <!-- Profile Section -->
      <div class="flex items-start gap-2 sm:gap-3 mb-3 sm:mb-4">
        <!-- Profile Picture -->
        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-lg flex-shrink-0"></div>
        
        <!-- Business Info -->
        <div class="flex-1 min-w-0">
          <!-- Business Name -->
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-3/4 sm:w-2/3 mb-1 sm:mb-2"></div>
          
          <!-- Business Tags -->
          <div class="flex gap-1 sm:gap-2 mb-1 sm:mb-2 flex-wrap">
            <div class="h-4 sm:h-5 bg-gray-200 rounded w-16 sm:w-20"></div>
            <div class="h-4 sm:h-5 bg-gray-200 rounded w-20 sm:w-24"></div>
          </div>
          
        </div>
      </div>
      
          <!-- Rating and Years -->
          <div class="flex items-center justify-between mb-2 sm:mb-3 pb-2 sm:pb-3">
            <div class="flex items-center gap-1 sm:gap-2">
              <div class="h-5 sm:h-6 bg-gray-200 rounded w-10 sm:w-12"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-16 sm:w-20"></div>
            </div>
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-28"></div>
          </div>
      <!-- Address Section -->
      <div class="border-t pt-3 sm:pt-4 mb-3 sm:mb-4">
        <div class="h-4 sm:h-5 bg-gray-200 rounded w-12 sm:w-16 mb-1 sm:mb-2"></div>
        <div class="h-3 sm:h-4 bg-gray-200 rounded w-full mb-1 sm:mb-2"></div>
        <div class="h-3 sm:h-4 bg-gray-200 rounded w-5/6 mb-2 sm:mb-3"></div>
        <div class="h-6 sm:h-8 bg-gray-200 rounded w-24 sm:w-32"></div>
      </div>
      
      <!-- Contact Buttons -->
      <div class="space-y-2 sm:space-y-3">
        <div class="h-10 sm:h-12 bg-gray-200 rounded w-full"></div>
        <div class="h-10 sm:h-12 bg-gray-200 rounded w-full"></div>
        <div class="h-10 sm:h-12 bg-gray-200 rounded w-full"></div>
      </div>
    </div>

    <!-- Business Products Skeleton -->
    <div v-else-if="type === 'business-products'" class="space-y-4 sm:space-y-6">
      <!-- Header skeleton -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 sm:mb-4 gap-3 sm:gap-0">
        <div class="h-6 sm:h-8 bg-gray-200 rounded w-24 sm:w-32"></div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 w-full sm:w-auto">
          <!-- Search bar skeleton -->
          <div class="h-8 sm:h-10 bg-gray-200 rounded w-full sm:w-48 lg:w-64"></div>
          <!-- Filter button skeleton -->
          <div class="flex items-center gap-2">
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-12 sm:w-16"></div>
            <div class="h-8 sm:h-10 bg-gray-200 rounded w-24 sm:w-32"></div>
          </div>
        </div>
      </div>

      <!-- Category sections skeleton -->
      <div v-for="i in 2" :key="i" class="mb-4 sm:mb-6">
        <!-- Category header skeleton -->
        <div class="h-5 sm:h-6 bg-gray-200 rounded w-32 sm:w-48 mb-3 sm:mb-4"></div>
        
        <!-- Products grid skeleton -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4">
          <div v-for="j in 4" :key="j" class="bg-white rounded-lg shadow-md overflow-hidden mb-2 sm:mb-3">
            <!-- Product image skeleton -->
            <div class="w-full h-32 sm:h-40 lg:h-48 bg-gray-200"></div>
            <!-- Product content skeleton -->
            <div class="p-2 sm:p-4 space-y-2 sm:space-y-3">
              <div class="h-4 sm:h-5 bg-gray-200 rounded w-3/4"></div>
              <div class="h-4 sm:h-6 bg-gray-200 rounded w-1/2"></div>
              <div class="h-8 sm:h-10 bg-gray-200 rounded w-full"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Business Profile Skeleton -->
    <div v-else-if="type === 'business-profile'" class="container mx-auto px-2 sm:px-4">
      <!-- Top Section Skeleton -->
      <div class="bg-white rounded-xl shadow p-3 sm:p-4 flex flex-col sm:flex-row items-start gap-3 sm:gap-4 mt-3 sm:mt-4">
        <!-- Business Image skeleton -->
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg bg-gray-200 flex-shrink-0"></div>
        
        <!-- Main Info skeleton -->
        <div class="sm:flex-1 min-w-0 w-full">
          <!-- Business name skeleton -->
          <div class="flex items-center gap-1 mb-2">
            <div class="h-6 sm:h-8 bg-gray-200 rounded w-32 sm:w-48"></div>
            <div class="h-5 sm:h-6 w-5 sm:w-6 bg-gray-200 rounded flex-shrink-0"></div>
          </div>
          
          <!-- Rating and tags skeleton -->
          <div class="flex items-center gap-1 flex-wrap mb-2 sm:mb-3">
            <div class="h-5 sm:h-6 bg-gray-200 rounded w-12 sm:w-16"></div>
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-16 sm:w-20 ml-1"></div>
            <div class="ml-2 flex gap-1 flex-wrap">
              <div class="h-4 sm:h-5 bg-gray-200 rounded w-16 sm:w-20"></div>
              <div class="h-4 sm:h-5 bg-gray-200 rounded w-20 sm:w-24"></div>
              <div class="h-4 sm:h-5 bg-gray-200 rounded w-16 sm:w-20"></div>
            </div>
          </div>
          
          <!-- Location and timing skeleton -->
          <div class="flex flex-wrap items-center gap-2 text-sm mb-2 sm:mb-3">
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-24 sm:w-32"></div>
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-32 sm:w-40"></div>
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-28 sm:w-36"></div>
          </div>
          
          <!-- Action buttons skeleton -->
          <div class="flex items-center gap-2 flex-wrap">
            <div class="h-8 sm:h-10 bg-gray-200 rounded w-24 sm:w-32"></div>
            <div class="h-6 sm:h-8 bg-gray-200 rounded w-20 sm:w-24"></div>
            <div class="h-6 sm:h-8 bg-gray-200 rounded w-20 sm:w-24"></div>
            <div class="h-6 sm:h-8 bg-gray-200 rounded w-24 sm:w-32"></div>
            <div class="h-8 sm:h-10 bg-gray-200 rounded w-28 sm:w-36"></div>
          </div>
        </div>
        
        <!-- Right side skeleton -->
        <div class="min-w-max flex flex-col h-24 sm:h-36 w-full sm:w-auto">
          <div class="flex gap-2 justify-end">
            <div class="h-6 sm:h-8 w-6 sm:w-8 bg-gray-200 rounded"></div>
            <div class="h-6 sm:h-8 w-6 sm:w-8 bg-gray-200 rounded"></div>
          </div>
          <div class="mt-auto">
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-24 mb-1 sm:mb-2"></div>
            <div class="h-6 sm:h-8 bg-gray-200 rounded w-16 sm:w-20"></div>
          </div>
        </div>
      </div>
      
      <!-- Main Section Skeleton -->
      <div class="container mx-auto mt-3 sm:mt-4">
        <!-- Tabs skeleton -->
        <div class="flex gap-3 sm:gap-6 ml-1 sm:ml-2 mb-3 sm:mb-4 overflow-x-auto">
          <div class="h-10 sm:h-12 bg-gray-200 rounded w-24 sm:w-32 flex-shrink-0"></div>
          <div class="h-10 sm:h-12 bg-gray-200 rounded w-20 sm:w-24 flex-shrink-0"></div>
          <div class="h-10 sm:h-12 bg-gray-200 rounded w-28 sm:w-36 flex-shrink-0"></div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6 mb-3 sm:mb-4">
          <!-- Main content skeleton -->
          <div class="flex-1 rounded-md shadow border p-3 sm:p-4">
            <div class="h-6 sm:h-8 bg-gray-200 rounded w-32 sm:w-48 mb-3 sm:mb-4"></div>
            <div class="space-y-3 sm:space-y-4">
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-full"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-5/6"></div>
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-4/5"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
              <div v-for="i in 6" :key="i" class="space-y-1 sm:space-y-2">
                <div class="h-3 sm:h-4 bg-gray-200 rounded w-20 sm:w-24"></div>
                <div class="h-4 sm:h-6 bg-gray-200 rounded w-24 sm:w-32"></div>
              </div>
            </div>
          </div>
          
          <!-- Sidebar skeleton -->
          <div class="w-full lg:w-80 flex-shrink-0">
            <div class="rounded-xl shadow px-3 sm:px-4 pt-3 sm:pt-4 pb-2 mb-3 sm:mb-4 border">
              <div class="h-5 sm:h-6 bg-gray-200 rounded w-16 sm:w-20 mb-3 sm:mb-4"></div>
              <div class="space-y-2 sm:space-y-3">
                <div v-for="i in 4" :key="i" class="h-8 sm:h-10 bg-gray-200 rounded"></div>
              </div>
              <div class="mt-3 sm:mt-4">
                <div class="h-5 sm:h-6 bg-gray-200 rounded w-12 sm:w-16 mb-3 sm:mb-4"></div>
                <div class="h-3 sm:h-4 bg-gray-200 rounded w-full mb-1 sm:mb-2"></div>
                <div class="h-3 sm:h-4 bg-gray-200 rounded w-5/6 mb-2 sm:mb-3"></div>
                <div class="h-6 sm:h-8 bg-gray-200 rounded w-24 sm:w-32"></div>
              </div>
              <div class="border-t mt-3 sm:mt-4 pt-3 sm:pt-4">
                <div class="space-y-2 sm:space-y-3">
                  <div v-for="i in 3" :key="i" class="h-8 sm:h-10 bg-gray-200 rounded"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Items Skeleton -->
    <div v-else-if="type === 'category-items'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-4">
      <div v-for="i in count" :key="i" class="flex items-center p-3 sm:p-4 rounded-lg border border-gray-100">
        <!-- Icon skeleton -->
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gray-200 mr-3 sm:mr-4 flex-shrink-0"></div>
        <!-- Text skeleton -->
        <div class="flex-1 min-w-0">
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <!-- Category Grid Skeleton -->
    <div v-else-if="type === 'category-grid'" class="py-6 sm:py-8 px-2 sm:px-4 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-6">
          <div v-for="i in count" :key="i" class="flex flex-col items-center">
            <!-- Icon container skeleton -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-xl mb-2 sm:mb-3"></div>
            <!-- Text skeleton -->
            <div class="h-3 sm:h-4 bg-gray-200 rounded w-16 sm:w-20"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category-wise Subcategories Skeleton -->
    <div v-else-if="type === 'category-wise-subcategories'" class="my-4 sm:my-6 bg-white">
      <div class="container mx-auto px-2 sm:px-4 py-3 sm:py-4">
        <!-- Header skeleton -->
        <div class="flex items-center justify-between mb-3 sm:mb-4">
          <div class="h-5 sm:h-6 bg-gray-200 rounded w-32 sm:w-48"></div>
          <div class="h-3 sm:h-4 bg-gray-200 rounded w-12 sm:w-16"></div>
        </div>

        <!-- Grid skeleton -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-4">
          <div v-for="i in count" :key="i" class="rounded-xl border border-gray-200 overflow-hidden bg-white">
            <!-- Image skeleton -->
            <div class="aspect-[4/3] bg-gray-200"></div>
            <!-- Text skeleton -->
            <div class="p-2 sm:p-3 text-center">
              <div class="h-3 sm:h-4 bg-gray-200 rounded w-3/4 mx-auto"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Generic skeleton -->
    <div v-else class="space-y-2 sm:space-y-3">
      <div v-for="i in count" :key="i" class="h-3 sm:h-4 bg-gray-200 rounded" :style="{ width: width }"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SkeletonLoader',
  props: {
    type: {
      type: String,
      default: 'generic',
      validator: value => ['product-card', 'business-card', 'product-details', 'reviews', 'related-products', 'related-categories', 'seller-information', 'business-profile', 'business-products', 'category-items', 'category-grid', 'category-wise-subcategories', 'generic'].includes(value)
    },
    count: {
      type: Number,
      default: 1
    },
    width: {
      type: String,
      default: '100%'
    }
  }
}
</script>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style> 