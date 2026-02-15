<template>
  <div>
    <div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-100 py-8">
      <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-6 md:mb-8">
          <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
            Add New Product
          </h1>
          <p class="text-gray-600 mt-2">Create and manage your product catalog</p>
        </div>

        <!-- Progress Steps -->
        <div class="flex justify-center mb-8">
          <div class="flex space-x-2 md:space-x-4">
            <div 
              v-for="(step, index) in steps" 
              :key="index"
              class="flex items-center"
            >
              <div 
                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
                :class="currentStep >= index 
                  ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg' 
                  : 'bg-gray-200 text-gray-500'"
              >
                {{ index + 1 }}
              </div>
              <span 
                class="ml-2 text-sm font-medium transition-colors duration-300 hidden md:inline"
                :class="currentStep >= index ? 'text-primary-600' : 'text-gray-400'"
              >
                {{ step }}
              </span>
              <div 
                v-if="index < steps.length - 1" 
                class="w-6 md:w-12 h-0.5 ml-2 md:ml-4 transition-all duration-300"
                :class="currentStep > index ? 'bg-gradient-to-r from-primary-500 to-primary-600' : 'bg-gray-200'"
              ></div>
            </div>
          </div>
        </div>

        <!-- Step 1: Basic Information -->
        <div v-if="currentStep === 0" class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 border border-primary-100">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </span>
            Basic Information
          </h2>
          
          <form @submit.prevent="nextStep" class="space-y-6">
            <!-- Row 1: Product Name and Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="product-name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                <input
                  v-model="form.name"
                  type="text"
                  id="product-name"
                  name="product-name"
                  :class="[
                    'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                    fieldErrors.name ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                  ]"
                  placeholder="Enter product name"
                  required
                />
                <div v-if="fieldErrors.name" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.name }}
                </div>
              </div>
              <!-- Stock -->
              <div>
                <label for="product-stock" class="block text-sm font-semibold text-gray-700 mb-2">Stock Status *</label>
                <select
                  v-model="form.stock"
                  id="product-stock"
                  name="product-stock"
                  :class="[
                    'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                    fieldErrors.stock ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                  ]"
                  required
                >
                  <option value="">Select stock status</option>
                  <option value="in stock">In Stock</option>
                  <option value="out of stock">Out of Stock</option>
                </select>
                <div v-if="fieldErrors.stock" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.stock }}
                </div>
              </div>
            </div>

            <!-- Row 1b: Business Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Business -->
              <div>
                <label for="business-id" class="block text-sm font-semibold text-gray-700 mb-2">Business *</label>
                <input
                  type="number"
                  v-model="form.business_id"
                  id="business-id"
                  name="business-id"
                  :class="[
                    'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                    fieldErrors.business_id ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                  ]"
                  required
                >
                <div v-if="fieldErrors.business_id" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.business_id }}
                </div>
              </div>
            </div>

            <!-- Row 2: Price and Per Unit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Price *</label>
                <div class="space-y-3">
                  <!-- Single Price Input -->
                  <div v-if="priceType === 'single'" class="relative">
                    <span class="absolute left-3 top-6 font-poppins transform -translate-y-1/2">৳</span>
                    <input
                      v-model="form.singlePrice"
                      type="number"
                      step="0.01"
                      min="0"
                      id="product-price-single"
                      name="product-price-single"
                      :class="[
                        'w-full pl-8 pr-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                        fieldErrors.price ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                      ]"
                      placeholder="0.00"
                      required
                      @input="formatPriceInput('singlePrice')"
                    />
                  </div>
                  
                  <!-- Price Range Inputs -->
                  <div v-if="priceType === 'range'" class="flex items-center space-x-3">
                    <div class="relative flex-1">
                      <span class="absolute left-3 top-6 font-poppins transform -translate-y-1/2">৳</span>
                      <input
                        v-model="form.priceRange.min"
                        type="number"
                        step="0.01"
                        min="0"
                        id="product-price-min"
                        name="product-price-min"
                        :class="[
                          'w-full pl-8 pr-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                          fieldErrors.price ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                        ]"
                        placeholder="0.00"
                        required
                        @input="formatPriceInput('priceRange.min')"
                      />
                    </div>
                    <span class="text-gray-500 font-medium">to</span>
                    <div class="relative flex-1">
                      <span class="absolute left-3 top-6 font-poppins transform -translate-y-1/2">৳</span>
                      <input
                        v-model="form.priceRange.max"
                        type="number"
                        step="0.01"
                        min="0"
                        id="product-price-max"
                        name="product-price-max"
                        :class="[
                          'w-full pl-8 pr-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                          fieldErrors.price ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                        ]"
                        placeholder="0.00"
                        required
                        @input="formatPriceInput('priceRange.max')"
                      />
                    </div>
                  </div>
                  
                  <!-- Price Type Selection -->
                  <div class="flex space-x-4 mt-3">
                    <label class="flex items-center">
                      <input
                        v-model="priceType"
                        type="radio"
                        value="single"
                        @change="onPriceTypeChange"
                        class="mr-2 text-primary-600 focus:ring-primary-500"
                      />
                      <span class="text-sm text-gray-700">Single Price</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        v-model="priceType"
                        type="radio"
                        value="range"
                        @change="onPriceTypeChange"
                        class="mr-2 text-primary-600 focus:ring-primary-500"
                      />
                      <span class="text-sm text-gray-700">Price Range</span>
                    </label>
                  </div>
                </div>
                <div v-if="fieldErrors.price" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.price }}
                </div>
                <p class="text-xs text-gray-500 mt-1">
                  Enter a single price (e.g., 100) or a price range (e.g., 90 - 100)
                </p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Per Unit *</label>
                <div class="flex items-center space-x-2">
                  <span class="text-primary-600 font-medium self-baseline">Per</span>
                  <input
                    v-model="form.unit_quantity"
                    type="number"
                    min="1" step="1"
                    class="w-20 self-baseline px-3 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 text-center hover:border-primary-200"
                    placeholder="1"
                    required
                  />
                  <div class="relative flex-1">
                    <input
                      :value="unitDropdown.search"
                      @input="unitDropdown.search = $event.target.value; debouncedUnitSearch()"
                      @focus="handleUnitFocus"
                      type="text"
                      :class="[
                        'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                        fieldErrors.product_unit_id ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                      ]"
                      placeholder="Type to search units..."
                      required
                    />
                    <div v-if="unitDropdown.isOpen" class="absolute z-10 w-full mt-1 bg-white border-2 border-primary-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                      <div v-if="unitsLoading" class="px-4 py-3 text-gray-500 text-center">
                        Loading units...
                      </div>
                      <div v-else-if="filteredUnits.length === 0" class="px-4 py-3 text-gray-500 text-center">
                        No units found
                      </div>
                      <div
                        v-else
                        v-for="unit in filteredUnits"
                        :key="unit.id"
                        @click="selectUnit(unit)"
                        class="px-4 py-3 hover:bg-primary-50 cursor-pointer transition-colors duration-200"
                      >
                        {{ unit.full_form }}
                      </div>
                    </div>
                    <div v-if="fieldErrors.product_unit_id" class="mt-1 text-sm text-red-600">
                      {{ fieldErrors.product_unit_id }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 4: Product Details -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Product Details *</label>
              <textarea
                v-model="form.details"
                rows="6"
                :class="[
                  'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 resize-none',
                  fieldErrors.details ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                ]"
                placeholder="Describe your product..."
                required
              ></textarea>
              <div v-if="fieldErrors.details" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.details }}
              </div>
            </div>
          </form>
        </div>

        <!-- Step 2: Specifications -->
        <div v-if="currentStep === 1" class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 border border-primary-100">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </span>
            Product Specifications
          </h2>
          
          <div class="space-y-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600">Add key-value pairs for product specifications</p>
                <p class="text-gray-600 text-xs md:text-sm">(e.g., Origin: Bangladesh)</p>
              </div>
              <button
                @click="addSpec"
                type="button"
                class="px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl"
              >
                + Add Specification
              </button>
            </div>

            <div v-for="(spec, index) in specifications" :key="index" class="flex flex-col md:flex-row items-stretch md:items-center gap-4 p-4 border-2 border-primary-100 rounded-xl bg-primary-50/30">
              <input
                v-model="spec.key"
                type="text"
                class="flex-1 px-4 py-2 border-2 border-primary-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                placeholder="Specification name"
              />
              <input
                v-model="spec.value"
                type="text"
                class="flex-1 px-4 py-2 border-2 border-primary-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                placeholder="Specification value"
              />
              <button
                @click="removeSpec(index)"
                type="button" class="self-end md:self-center px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-300"
              >
                ✕
              </button>
            </div>

            <div v-if="specifications.length === 0" class="text-center py-8 text-gray-500">
              <p>No specifications added yet. Click "Add Specification" to get started.</p>
            </div>
            <div v-if="fieldErrors.specifications" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.specifications }}
            </div>
          </div>
        </div>

        <!-- Step 3: Categories -->
        <div v-if="currentStep === 2" class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 border border-primary-100">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
            </span>
            Product Categories
          </h2>
          
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Search Categories</label>
              <div class="text-sm text-gray-600 mb-2">
                Selected: {{ selectedCategories.length }}/{{ maxCategories }}
              </div>
              <div class="relative">
                <input
                  @focus="handleCategoryFocus"
                  :value="categoryDropdown.search"
                  @input="categoryDropdown.search = $event.target.value; debouncedCategorySearch()"
                  type="text"
                  :class="[
                    'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                    fieldErrors.categories ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                  ]"
                  :disabled="selectedCategories.length >= maxCategories"
                  placeholder="Type to search categories..."
                />
                <div v-if="categoryDropdown.isOpen" class="absolute z-10 w-full mt-1 bg-white border-2 border-primary-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                  <div v-if="categoriesLoading" class="px-4 py-3 text-gray-500 text-center">
                    Loading categories...
                  </div>
                  <div v-else-if="filteredCategories.length === 0" class="px-4 py-3 text-gray-500 text-center">
                    No categories found
                  </div>
                  <div
                    v-else
                    v-for="category in filteredCategories"
                    :key="category.id"
                    @click="selectCategory(category)"
                    class="px-4 py-3 hover:bg-primary-50 cursor-pointer transition-colors duration-200"
                    :class="{
                      'opacity-50 cursor-not-allowed': selectedCategories.length >= maxCategories,
                      'hover:bg-primary-50': selectedCategories.length < maxCategories
                    }"
                  >
                    {{ category.parent }} > {{ category.name }}
                    <span v-if="isCategorySelected(category.id)" class="ml-2 text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Selected</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="selectedCategories.length > 0">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Selected Categories</label>
              <div class="flex flex-wrap gap-2">
                <div
                  v-for="category in selectedCategories"
                  :key="category.id"
                  class="flex items-center space-x-2 px-3 py-2 bg-primary-100 text-primary-800 rounded-lg"
                >
                  <span>{{ category.name }}</span>
                  <button
                    @click="removeCategory(category.id)"
                    class="text-primary-600 hover:text-primary-800"
                  >
                    ✕
                  </button>
                </div>
              </div>
            </div>

            <div v-if="selectedCategories.length === 0" class="text-center py-8 text-gray-500">
              <p>No categories selected yet. Search and select categories above.</p>
            </div>
            
            <div v-if="fieldErrors.categories" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.categories }}
            </div>
          </div>
        </div>

        <!-- Step 4: Gallery -->
        <div v-if="currentStep === 3" class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 border border-primary-100">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </span>
            Product Gallery
          </h2>
          
          <div class="space-y-6">
            <!-- YouTube Video URL -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube Video</label>
              <input
                v-model="form.video_url"
                type="url"
                :class="[
                  'w-full px-4 py-3 border-2 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300',
                  fieldErrors.video_url ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 hover:border-primary-200 focus:ring-primary-500'
                ]"
                placeholder="https://www.youtube.com/watch?v=..."
              />
              <div v-if="fieldErrors.video_url" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.video_url }}
              </div>
              <p class="text-xs text-gray-500 mt-1">
                Optional: Add a YouTube video URL to showcase your product
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Images</label>
              <div class="border-2 border-dashed border-primary-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors duration-300">
                <input
                  @change="onImageChange"
                  type="file"
                  multiple
                  accept="image/*"
                  class="hidden"
                  id="image-upload"
                />
                <label for="image-upload" class="cursor-pointer">
                  <svg class="mx-auto h-12 w-12 text-primary-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <p class="mt-2 text-sm text-primary-600">Click to upload images</p>
                  <p class="mt-1 text-xs text-gray-500">JPEG, JPG, PNG, and WEBP up to 5MB</p>
                </label>
              </div>
              <p class="text-sm text-gray-500 mt-2">First image will be the feature image. Drag to reorder.</p>
              <p class="text-sm text-gray-600 mt-1">Images: {{ images.length }}/10</p>
            </div>

            <div v-if="images.length > 0">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Product Images</label>
              <draggable v-model="images" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" item-key="index">
                <template #item="{ element: image, index }">
                  <div class="relative group aspect-square">
                    <img
                      :src="imageSrc(image)"
                      :alt="`Product image ${index + 1}`"
                      class="w-full h-full object-cover rounded-xl border-4 border-gray-300"
                      :class="{ 'border-primary': index == 0 }"
                    />
                    
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-xl flex items-center justify-center">
                      <button 
                        @click.stop="removeImage(index)"
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        ×
                      </button>
                    </div>
                    <div v-if="index === 0" class="absolute top-2 left-2 bg-primary text-white text-xs px-2 py-1 rounded-full">
                      Feature
                    </div>
                  </div>
                </template>
              </draggable>
            </div>

            <div v-if="images.length === 0" class="text-center py-8 text-gray-500">
              <p>No images uploaded yet. Upload images above to get started.</p>
            </div>
            
            <div v-if="fieldErrors.images" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.images }}
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-primary-100 text-sm md:text-base">
          <button
            @click="prevStep"
            v-if="currentStep > 0"
            class="px-3 md:px-6 py-3 border-2 border-primary-200 text-primary-700 rounded-xl hover:border-primary-300 hover:bg-primary-50 transition-all duration-300"
          >
            ← Previous
          </button>
          <div v-else></div>

          <div class="flex space-x-4">
            <button
              v-if="currentStep < 3"
              @click="nextStep"
              class="px-3 md:px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              Next →
            </button>
            <button
              v-else
              @click="onSubmit"
              :disabled="isSubmitting"
              class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isSubmitting">Creating Product...</span>
              <span v-else>Create Product</span>
            </button>
          </div>
        </div>

        <!-- Messages -->
        <div v-if="successMessage" class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
          {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
          {{ errorMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import draggable from 'vuedraggable';
import { productAPI, productUnitAPI, categoryAPI } from '../api/services.js';
import { push } from 'notivue';
import { validateImageFile } from '../utils/imageValidation.js';

export default {
  name: 'AddProduct',
  components: { draggable },
  setup() {
    const router = useRouter();
    const route = useRoute();
    const currentStep = ref(0);
    const steps = ['Basic Info', 'Specifications', 'Categories', 'Gallery'];
    const isSubmitting = ref(false);
    const successMessage = ref('');
    const errorMessage = ref('');
    
    const form = ref({
      name: '',
      details: '',
      price: '',
      singlePrice: '',
      priceRange: {
        min: '',
        max: ''
      },
      specification: {},
      unit_quantity: 1,
      product_unit_id: 8,
      stock: 'in stock',
      category_ids: [],
      status: 'active',
      video_url: '',
      business_id: route.query.business_id ?? null
    });

    const priceType = ref('single');
    const specifications = ref([]);
    const selectedCategories = ref([]);
    const maxCategories = ref(3);
    const images = ref([]);
    
    const unitDropdown = ref({
      isOpen: false,
      search: 'Piece'
    });
    
    const categoryDropdown = ref({
      isOpen: false,
      search: ''
    });

    const productUnits = ref([]);
    const productCategories = ref([]);
    const businesses = ref([]);
    const unitsLoading = ref(false);
    const categoriesLoading = ref(false);

    const fieldErrors = ref({
      name: '',
      details: '',
      price: '',
      product_unit_id: '',
      unit_quantity: '',
      stock: '',
      categories: '',
      images: '',
      specifications: '',
      video_url: '',
      business_id: ''
    });

    const videoPreview = ref(null);

    // Computed properties
    const filteredUnits = computed(() => {
      return productUnits.value.filter(unit =>
        unit.full_form.toLowerCase().includes(unitDropdown.value.search.toLowerCase())
      );
    });

    const filteredCategories = computed(() => {
      return productCategories.value.filter(category => 
        !isCategorySelected(category.id) && 
        (category.name.toLowerCase().includes(categoryDropdown.value.search.toLowerCase()) ||
         category.parent.toLowerCase().includes(categoryDropdown.value.search.toLowerCase()))
      );
    });

    const isCategorySelected = (categoryId) => {
      return selectedCategories.value.some(cat => cat.id === categoryId);
    };

    // Methods
    const nextStep = () => {
      if (currentStep.value === 0 && !validateStep1()) return;
      if (currentStep.value === 1 && !validateStep2()) return;
      if (currentStep.value === 2 && !validateStep3()) return;
      
      if (currentStep.value < 3) {
        currentStep.value++;
        errorMessage.value = '';
        clearFieldErrors();
      }
    };

    const prevStep = () => {
      if (currentStep.value > 0) {
        currentStep.value--;
      }
    };

    const addSpec = () => {
      specifications.value.push({ key: '', value: '' });
    };

    const removeSpec = (index) => {
      specifications.value.splice(index, 1);
    };

    const selectUnit = (unit) => {
      form.value.product_unit_id = unit.id;
      unitDropdown.value.search = unit.full_form;
      unitDropdown.value.isOpen = false;
    };

    const selectCategory = (category) => {
      if (selectedCategories.value.length >= maxCategories.value) {
        push.warning(`You can select a maximum of ${maxCategories.value} categories.`);
        return;
      }
      if (!selectedCategories.value.find(c => c.id === category.id)) {
        selectedCategories.value.push(category);
        form.value.category_ids.push(category.id);
      }
      categoryDropdown.value.search = '';
      categoryDropdown.value.isOpen = false;
    };

    const removeCategory = (categoryId) => {
      selectedCategories.value = selectedCategories.value.filter(c => c.id !== categoryId);
      form.value.category_ids = form.value.category_ids.filter(id => id !== categoryId);
    };

    const onImageChange = (event) => {
      const files = Array.from(event.target.files);
      
      if (images.value.length + files.length > 10) {
        const availableSlots = 10 - images.value.length;
        if (availableSlots > 0) {
          push.error(`You can only upload ${availableSlots} more image(s). Maximum 10 images allowed.`);
        } else {
          push.error(`Maximum 10 images reached. Cannot upload more images.`);
        }
        return;
      }
      
      files.forEach(file => {
        const validation = validateImageFile(file);
        if (!validation.isValid) {
          push.error(validation.error);
          return;
        }
        const preview = URL.createObjectURL(file);
        images.value.push({ file, preview });
      });
    };

    const removeImage = (index) => {
      images.value.splice(index, 1);
    };

    const onPriceTypeChange = () => {
      if (priceType.value === 'single') {
        form.value.priceRange.min = '';
        form.value.priceRange.max = '';
      } else if (priceType.value === 'range') {
        form.value.singlePrice = '';
      }
      fieldErrors.value.price = '';
    };

    const debouncedUnitSearch = (() => {
      let timeout;
      return () => {
        unitDropdown.value.isOpen = true;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          searchUnits();
        }, 300);
      };
    })();

    const debouncedCategorySearch = (() => {
      let timeout;
      return () => {
        categoryDropdown.value.isOpen = true;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          searchCategories();
        }, 300);
      };
    })();

    const searchUnits = async () => {
      try {
        unitsLoading.value = true;
        const response = await productUnitAPI.getAll();
        productUnits.value = response.data.data || [];
      } catch (error) {
        console.error('Error searching units:', error);
      } finally {
        unitsLoading.value = false;
      }
    };

    const searchCategories = async () => {
      try {
        categoriesLoading.value = true;
        const response = await categoryAPI.productCategories({ keyword: categoryDropdown.value.search });
        productCategories.value = response.data.data || [];
      } catch (error) {
        console.error('Error searching categories:', error);
      } finally {
        categoriesLoading.value = false;
      }
    };

    const searchBusinesses = async () => {
      try {
        const response = await productAPI.getBusinesses?.();
        if (response) {
          businesses.value = response.data?.data || [];
        }
      } catch (error) {
        console.error('Error fetching businesses:', error);
        // Fallback: try to fetch from businessAPI if available
      }
    };

    const validateStep1 = () => {
      clearFieldErrors();
      let isValid = true;
      
      if (!form.value.name) {
        fieldErrors.value.name = 'Please enter product name';
        isValid = false;
      }
      if (!form.value.details) {
        fieldErrors.value.details = 'Please enter product details';
        isValid = false;
      }
      if (priceType.value === 'single') {
        if (!form.value.singlePrice || form.value.singlePrice <= 0) {
          fieldErrors.value.price = 'Please enter a valid single price';
          isValid = false;
        }
      } else if (priceType.value === 'range') {
        if (!form.value.priceRange.min || !form.value.priceRange.max) {
          fieldErrors.value.price = 'Please enter both minimum and maximum prices';
          isValid = false;
        } else if (form.value.priceRange.min <= 0 || form.value.priceRange.max <= 0) {
          fieldErrors.value.price = 'Prices must be greater than 0';
          isValid = false;
        } else if (parseFloat(form.value.priceRange.min) >= parseFloat(form.value.priceRange.max)) {
          fieldErrors.value.price = 'Minimum price must be less than maximum price';
          isValid = false;
        }
      }
      if (!form.value.product_unit_id) {
        fieldErrors.value.product_unit_id = 'Please select a product unit';
        isValid = false;
      }
      if (!form.value.unit_quantity || form.value.unit_quantity <= 0) {
        push.error('Please enter a valid unit quantity');
        isValid = false;
      } else if (!Number.isInteger(Number(form.value.unit_quantity))) {
        push.error('Unit quantity must be a whole number');
        isValid = false;
      }
      
      if (!form.value.stock) {
        fieldErrors.value.stock = 'Please select stock status';
        isValid = false;
      }

      if (!form.value.business_id) {
        fieldErrors.value.business_id = 'Please select a business';
        isValid = false;
      }
      
      if (form.value.video_url && !isValidYouTubeUrl(form.value.video_url)) {
        fieldErrors.value.video_url = 'Please enter a valid YouTube URL';
        isValid = false;
      }
      
      return isValid;
    };

    const validateStep2 = () => {
      clearFieldErrors();
      const hasAtLeastOneCompleteSpec = specifications.value.some((spec) => spec.key && spec.value);

      if (!hasAtLeastOneCompleteSpec) {
        fieldErrors.value.specifications = 'Please add at least one specification and fill in both key and value.';
        return false;
      }
      return true;
    };

    const validateStep3 = () => {
      clearFieldErrors();
      if (selectedCategories.value.length === 0) {
        fieldErrors.value.categories = 'Please select at least one category';
        return false;
      }
      return true;
    };

    const validateStep4 = () => {
      clearFieldErrors();
      if (images.value.length === 0) {
        fieldErrors.value.images = 'Please upload at least one image';
        return false;
      }
      return true;
    };

    const validateForm = () => {
      return validateStep1() && validateStep2() && validateStep3() && validateStep4();
    };

    const clearFieldErrors = () => {
      fieldErrors.value = {
        name: '',
        details: '',
        price: '',
        product_unit_id: '',
        unit_quantity: '',
        stock: '',
        categories: '',
        images: '',
        specifications: '',
        video_url: ''
      };
    };

    const isValidYouTubeUrl = (url) => {
      const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/(watch\?v=)?([a-zA-Z0-9_-]{11})/;
      return youtubeRegex.test(url);
    };

    const formatPriceInput = (fieldName) => {
      let value;
      if (fieldName === 'singlePrice') {
        value = form.value.singlePrice;
        if (value !== '') {
          const formattedValue = parseFloat(parseFloat(value).toFixed(2));
          if (!isNaN(formattedValue)) {
            form.value.singlePrice = formattedValue;
          } else {
            form.value.singlePrice = '';
          }
        }
      } else if (fieldName.startsWith('priceRange')) {
        if (fieldName === 'priceRange.min') {
          value = form.value.priceRange.min;
          if (value !== '') {
            const formattedValue = parseFloat(parseFloat(value).toFixed(2));
            if (!isNaN(formattedValue)) {
              form.value.priceRange.min = formattedValue;
            } else {
              form.value.priceRange.min = '';
            }
          }
        } else if (fieldName === 'priceRange.max') {
          value = form.value.priceRange.max;
          if (value !== '') {
            const formattedValue = parseFloat(parseFloat(value).toFixed(2));
            if (!isNaN(formattedValue)) {
              form.value.priceRange.max = formattedValue;
            } else {
              form.value.priceRange.max = '';
            }
          }
        }
      }
    };

    const handleUnitFocus = () => {
      unitDropdown.value.isOpen = true;
      if (productUnits.value.length === 0) {
        searchUnits();
      }
    };

    const handleCategoryFocus = () => {
      categoryDropdown.value.isOpen = true;
      if (productCategories.value.length === 0) {
        searchCategories();
      }
    };

    const prepareProductData = () => {
      const specification = {};
      specifications.value.forEach(spec => {
        if (spec.key && spec.value) {
          specification[spec.key] = spec.value;
        }
      });
      
      let formattedPrice = '';
      if (priceType.value === 'single') {
        formattedPrice = form.value.singlePrice.toString();
      } else if (priceType.value === 'range') {
        formattedPrice = `${form.value.priceRange.min} - ${form.value.priceRange.max}`;
      }
      
      return {
        name: form.value.name,
        details: form.value.details,
        price: formattedPrice,
        specification,
        unit_quantity: form.value.unit_quantity,
        product_unit_id: form.value.product_unit_id,
        stock: form.value.stock,
        category_ids: form.value.category_ids,
        video_url: form.value.video_url,
        business_id: form.value.business_id, // Admin creates for any business
        status: 'active'
      };
    };

    const imageSrc = (image) => {
      const raw = image?.url || image?.preview || image;
      if (!raw) return '';
      if (typeof raw !== 'string') return raw;
      // blob URLs produced by createObjectURL should be returned directly
      if (raw.startsWith('http') || raw.startsWith('data:') || raw.startsWith('blob:')) return raw;
      try {
        const apiBase = import.meta.env.VITE_API_BASE_URL || '';
        if (!apiBase) return raw;
        const origin = new URL(apiBase).origin;
        if (raw.startsWith('/')) return origin + raw;
        return origin + '/' + raw.replace(/^\/+/, '');
      } catch (e) {
        return raw;
      }
    };

    const onSubmit = async () => {
      if (!validateForm()) {
        return;
      }
      
      errorMessage.value = '';
      successMessage.value = '';
      
      try {
        isSubmitting.value = true;
        const productData = prepareProductData();
        
        // Create product
        const response = await productAPI.create(productData);
        const productId = response.data.data.id;

        if (images.value.length > 0) {
          try {
            const formData = new FormData();
            images.value.forEach((img, index) => {
              if (img.file) {
                formData.append('images[]', img.file);
              }
            });
            
            await productAPI.uploadImages(productId, formData);
            push.success('Product created successfully with images!');
          } catch (imageError) {
            console.warn('Image upload failed, but product was created:', imageError);
            push.success('Product created successfully! (Images will be uploaded later)');
          }
        } else {
          push.success('Product created successfully!');
        }

        setTimeout(() => {
          router.push('/products');
        }, 1500);
        
      } catch (error) {
        console.error('Error creating product:', error);
        push.error(error.response?.data?.message || 'Failed to create product. Please try again.');
      } finally {
        isSubmitting.value = false;
      }
    };

    // Initialize
    searchUnits();
    searchCategories();
    searchBusinesses();

    return {
      currentStep,
      steps,
      form,
      priceType,
      specifications,
      selectedCategories,
      maxCategories,
      images,
      unitDropdown,
      categoryDropdown,
      productUnits,
      productCategories,
      businesses,
      unitsLoading,
      categoriesLoading,
      fieldErrors,
      videoPreview,
      isSubmitting,
      successMessage,
      errorMessage,
      filteredUnits,
      filteredCategories,
      isCategorySelected,
      nextStep,
      prevStep,
      addSpec,
      removeSpec,
      selectUnit,
      selectCategory,
      removeCategory,
      onImageChange,
      removeImage,
      onPriceTypeChange,
      debouncedUnitSearch,
      debouncedCategorySearch,
      searchUnits,
      searchCategories,
      validateForm,
      clearFieldErrors,
      isValidYouTubeUrl,
      formatPriceInput,
      handleUnitFocus,
      handleCategoryFocus,
      onSubmit,
      imageSrc
    };
  }
};
</script>

<style scoped>
.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

.from-primary-500 {
  --tw-gradient-from: #0B845C;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(11, 132, 92, 0));
}

.to-primary-600 {
  --tw-gradient-to: #0A6B4A;
}

.from-primary-600 {
  --tw-gradient-from: #0A6B4A;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(10, 107, 74, 0));
}

.to-primary-700 {
  --tw-gradient-to: #08523A;
}

.from-green-500 {
  --tw-gradient-from: #10B981;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(16, 185, 129, 0));
}

.to-emerald-500 {
  --tw-gradient-to: #10B981;
}

.from-green-600 {
  --tw-gradient-from: #059669;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(5, 150, 105, 0));
}

.to-emerald-600 {
  --tw-gradient-to: #059669;
}

.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}
</style>
