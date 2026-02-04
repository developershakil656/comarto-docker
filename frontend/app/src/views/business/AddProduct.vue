<template>
  <div>
    <AdminDashboardHeader title="Add Product"/>
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
              <!-- <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Status *</label>
                <select
                  v-model="form.status"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 hover:border-primary-200"
                  required
                >
                  <option value="">Select status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div> -->
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
                      v-model="unitDropdown.search"
                    @focus="handleUnitFocus"
                    @input="debouncedUnitSearch"
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
                  v-model="categoryDropdown.search"
                  @focus="handleCategoryFocus"
                  @input="debouncedCategorySearch"
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
                    {{ category.name }}
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

            <!-- Video Preview Section -->
            <div v-if="videoPreview && !fieldErrors.video_url" class="mb-4">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Video Preview</h3>
              <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50 max-w-xs">
                <div class="aspect-video relative">
                  <img 
                    :src="videoPreview.thumbnail" 
                    :alt="videoPreview.title"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                    <div class="bg-red-600 text-white rounded-full p-2">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="p-3">
                  <h4 class="font-medium text-gray-900 text-xs line-clamp-2">{{ videoPreview.title }}</h4>
                  <p class="text-gray-500 text-xs mt-1">{{ videoPreview.duration }}</p>
                </div>
              </div>
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
                  <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                </label>
              </div>
              <p class="text-sm text-gray-500 mt-2">First image will be the feature image. Drag to reorder.</p>
            </div>

            <div v-if="images.length > 0">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Product Images</label>
              <draggable
                v-model="images"
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 cursor-move"
                :options="{ animation: 200, ghostClass: 'sortable-ghost' }"
                @start="drag = true"
                @end="drag = false"
                item-key="index"
              >
                <template #item="{ element: image, index }">
                  <div class="relative group">
                    <img
                      :src="image.preview || image"
                      :alt="`Product image ${index + 1}`"
                      class="w-full h-32 object-cover rounded-xl border-4 border-gray-300"
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
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue';
import draggable from 'vuedraggable';
import { mapActions, mapGetters } from 'vuex';
import { push } from 'notivue';
import { ref, computed, watch, nextTick } from 'vue';
import { validateImageFile } from '@/utils/imageValidation.js';

export default {
  name: 'AddProduct',
  components: { 
    AdminDashboardHeader,
    draggable
  },
  data() {
    return {
      currentStep: 0,
      steps: ['Basic Info', 'Specifications', 'Categories', 'Gallery'],
      drag: false,
      successMessage: '',
      errorMessage: '',
      form: {
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
        product_unit_id: null,
        stock: '',
        category_ids: [],
        status: '',
        video_url: ''
      },
      priceType: 'single', // Default to single price
      specifications: [],
      selectedCategories: [],
        maxCategories: 3,
      images: [],
      unitDropdown: {
        isOpen: false,
        search: ''
      },
      categoryDropdown: {
        isOpen: false,
        search: ''
      },
      // Add search timeouts for debouncing
      searchTimeout: null,
      categorySearchTimeout: null,
      unitSearchTimeout: null,
      // Field-level validation errors
      fieldErrors: {
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
      },
      videoPreview: null // New data property for video preview
    }
  },
  computed: {
    ...mapGetters([
      'productCreation',
      'productUnits',
      'productCategories',
      'unitsLoading',
      'categoriesLoading'
    ]),
    filteredUnits() {
      return this.safeProductUnits;
    },
    filteredCategories() {
      // Filter out already selected categories
      return this.safeProductCategories.filter(category => 
        !this.isCategorySelected(category.id)
      );
    },
    isCategorySelected() {
      return (categoryId) => {
        return this.selectedCategories.some(cat => cat.id === categoryId);
      };
    },
    isSubmitting() {
      return this.productCreation.loading;
    },
    // Safe data getters with fallbacks
    safeProductUnits() {
      return Array.isArray(this.productUnits) ? this.productUnits : [];
    },
    safeProductCategories() {
      return Array.isArray(this.productCategories) ? this.productCategories : [];
    }
  },
  watch: {
    // Auto-detect price type based on user input
    'form.singlePrice'(newVal) {
      if (newVal && this.priceType !== 'single') {
        this.priceType = 'single';
        // Clear range values when switching to single
        this.form.priceRange.min = '';
        this.form.priceRange.max = '';
      }
    },
    'form.priceRange.min'(newVal) {
      if (newVal && this.priceType !== 'range') {
        this.priceType = 'range';
        // Clear single price when switching to range
        this.form.singlePrice = '';
      }
    },
    'form.priceRange.max'(newVal) {
      if (newVal && this.priceType !== 'range') {
        this.priceType = 'range';
        // Clear single price when switching to range
        this.form.singlePrice = '';
      }
    },
    // Watch for video_url changes to fetch preview
    'form.video_url'(newVal) {
      if (newVal) {
        this.fetchVideoPreview(newVal);
      } else {
        this.videoPreview = null;
      }
    }
  },
  mounted() {
    this.searchUnits();
    this.searchCategories();
    this.addClickOutsideListeners();
    // Initial fetch for video preview if URL is already set
    if (this.form.video_url) {
      this.fetchVideoPreview(this.form.video_url);
    }
  },
  beforeUnmount() {
    this.removeClickOutsideListeners();
    // Clear all timeouts
    if (this.searchTimeout) clearTimeout(this.searchTimeout);
    if (this.categorySearchTimeout) clearTimeout(this.categorySearchTimeout);
    if (this.unitSearchTimeout) clearTimeout(this.unitSearchTimeout);
  },
  methods: {
    ...mapActions([
      'storeProduct',
      'uploadProductImages',
      'searchProductUnits',
      'searchProductCategories'
    ]),
    nextStep() {
      // Validate current step before proceeding
      if (this.currentStep === 0 && !this.validateStep1()) {
        return;
      }
      if (this.currentStep === 1 && !this.validateStep2()) {
        return;
      }
      if (this.currentStep === 2 && !this.validateStep3()) {
        return;
      }
      
      if (this.currentStep < 3) {
        this.currentStep++;
        // Clear any previous error messages when moving to next step
        this.errorMessage = '';
        this.clearFieldErrors();
      }
    },
    prevStep() {
      if (this.currentStep > 0) {
        this.currentStep--;
      }
    },
    addSpec() {
      this.specifications.push({ key: '', value: '' });
    },
    removeSpec(index) {
      this.specifications.splice(index, 1);
    },
    selectUnit(unit) {
      this.form.product_unit_id = unit.id;
      this.unitDropdown.search = unit.full_form;
      this.unitDropdown.isOpen = false;
    },
    selectCategory(category) {
      if (this.selectedCategories.length >= this.maxCategories) {
        push.warning(`You can select a maximum of ${this.maxCategories} categories.`);
        return;
      }
      if (!this.selectedCategories.find(c => c.id === category.id)) {
        this.selectedCategories.push(category);
        this.form.category_ids.push(category.id);
      }
      this.categoryDropdown.search = '';
      this.categoryDropdown.isOpen = false;
    },
    removeCategory(categoryId) {
      this.selectedCategories = this.selectedCategories.filter(c => c.id !== categoryId);
      this.form.category_ids = this.form.category_ids.filter(id => id !== categoryId);
    },
    onImageChange(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
        const validation = validateImageFile(file);
        if (!validation.isValid) {
          push.error(validation.error);
          return;
        }
        const preview = URL.createObjectURL(file);
        this.images.push({ file, preview });
      });
    },
    removeImage(index) {
      this.images.splice(index, 1);
    },
    onPriceTypeChange() {
      // Clear the other price type when switching
      if (this.priceType === 'single') {
        this.form.priceRange.min = '';
        this.form.priceRange.max = '';
      } else if (this.priceType === 'range') {
        this.form.singlePrice = '';
      }
      // Clear any price validation errors
      this.fieldErrors.price = '';
    },
    // Debounced search for units
    debouncedUnitSearch() {
      // Keep dropdown open while typing
      this.unitDropdown.isOpen = true;
      clearTimeout(this.unitSearchTimeout);
      this.unitSearchTimeout = setTimeout(() => {
        this.searchUnits();
      }, 300);
    },
    // Debounced search for categories
    debouncedCategorySearch() {
      // Keep dropdown open while typing
      this.categoryDropdown.isOpen = true;
      clearTimeout(this.categorySearchTimeout);
      this.categorySearchTimeout = setTimeout(() => {
        this.searchCategories();
      }, 300);
    },
    async searchUnits() {
      try {
        await this.searchProductUnits(this.unitDropdown.search || '');
      } catch (error) {
        console.error('Error searching units:', error);
      }
    },
    async searchCategories() {
      try {
        await this.searchProductCategories(this.categoryDropdown.search || '');
      } catch (error) {
        console.error('Error searching categories:', error);
      }
    },
    // Step 1 validation: Basic Information
    validateStep1() {
      // Clear previous field errors
      this.clearFieldErrors();
      
      let isValid = true;
      
      if (!this.form.name) {
        this.fieldErrors.name = 'Please enter product name';
        isValid = false;
      }
      if (!this.form.details) {
        this.fieldErrors.details = 'Please enter product details';
        isValid = false;
      }
      // Validate price based on type
      if (this.priceType === 'single') {
        if (!this.form.singlePrice || this.form.singlePrice <= 0) {
          this.fieldErrors.price = 'Please enter a valid single price';
          isValid = false;
        }
      } else if (this.priceType === 'range') {
        if (!this.form.priceRange.min || !this.form.priceRange.max) {
          this.fieldErrors.price = 'Please enter both minimum and maximum prices';
          isValid = false;
        } else if (this.form.priceRange.min <= 0 || this.form.priceRange.max <= 0) {
          this.fieldErrors.price = 'Prices must be greater than 0';
          isValid = false;
        } else if (parseFloat(this.form.priceRange.min) >= parseFloat(this.form.priceRange.max)) {
          this.fieldErrors.price = 'Minimum price must be less than maximum price';
          isValid = false;
        }
      }
      if (!this.form.product_unit_id) {
        this.fieldErrors.product_unit_id = 'Please select a product unit';
        isValid = false;
      }
      if (!this.form.unit_quantity || this.form.unit_quantity <= 0) {
        push.error('Please enter a valid unit quantity');
        // this.fieldErrors.unit_quantity = 'Please enter a valid unit quantity';
        isValid = false;
      } else if (!Number.isInteger(Number(this.form.unit_quantity))) {
        push.error('Unit quantity must be a whole number');
        // this.fieldErrors.unit_quantity = 'Unit quantity must be a whole number';
        isValid = false;
      }
      
      if (!this.form.stock) {
        this.fieldErrors.stock = 'Please select stock status';
        isValid = false;
      }
      
      // Validate YouTube URL if provided
      if (this.form.video_url && !this.isValidYouTubeUrl(this.form.video_url)) {
        this.fieldErrors.video_url = 'Please enter a valid YouTube URL';
        isValid = false;
      }
      
      return isValid;
    },
    
    // Step 2 validation: Specifications (at least one complete key/value required)
    validateStep2() {
      // Clear previous field errors
      this.clearFieldErrors();

      const hasAtLeastOneCompleteSpec = this.specifications.some((spec) => spec.key && spec.value);

      if (!hasAtLeastOneCompleteSpec) {
        this.fieldErrors.specifications = 'Please add at least one specification and fill in both key and value.';
        return false;
      }
      return true;
    },
    
    // Step 3 validation: Categories
    validateStep3() {
      // Clear previous field errors
      this.clearFieldErrors();
      
      if (this.selectedCategories.length === 0) {
        this.fieldErrors.categories = 'Please select at least one category';
        return false;
      }
      return true;
    },
    
    // Step 4 validation: Images
    validateStep4() {
      // Clear previous field errors
      this.clearFieldErrors();
      
      if (this.images.length === 0) {
        this.fieldErrors.images = 'Please upload at least one image';
        return false;
      }
      return true;
    },
    
    // Final form validation (used when submitting)
    validateForm() {
      if (!this.validateStep1()) {
        return false;
      }
      if (!this.validateStep2()) {
        return false;
      }
      if (!this.validateStep3()) {
        return false;
      }
      if (!this.validateStep4()) {
        return false;
      }
      return true;
    },
    prepareProductData() {
      // Convert specifications array to object
      const specification = {};
      this.specifications.forEach(spec => {
        if (spec.key && spec.value) {
          specification[spec.key] = spec.value;
        }
      });
      
      // Format price based on type
      let formattedPrice = '';
      if (this.priceType === 'single') {
        formattedPrice = this.form.singlePrice.toString();
      } else if (this.priceType === 'range') {
        formattedPrice = `${this.form.priceRange.min} - ${this.form.priceRange.max}`;
      }
      
      return {
        name: this.form.name,
        details: this.form.details,
        price: formattedPrice,
        specification,
        unit_quantity: this.form.unit_quantity,
        product_unit_id: this.form.product_unit_id,
        stock: this.form.stock,
        category_ids: this.form.category_ids,
        video_url: this.form.video_url
      };
    },
    async onSubmit() {
      if (!this.validateForm()) {
        return;
      }
      
      this.errorMessage = '';
      this.successMessage = '';
      
      try {
        const productData = this.prepareProductData();
        const productResult = await this.storeProduct(productData);
        
        if (productResult && productResult.id) {
          // Product created successfully - try to upload images but don't fail if it doesn't work
          try {
            // Map to file objects for upload
            const files = this.images.map(img => (img && img.file) ? img.file : img);
            await this.uploadProductImages({
              productId: productResult.id,
              images: files
            });
            push.success('Product created successfully with images!');
          } catch (imageError) {
            console.warn('Image upload failed, but product was created:', imageError);
            push.success('Product created successfully! (Images will be uploaded later)');
          }
          
          // Redirect to business dashboard after successful product creation
          setTimeout(() => {
            this.$router.push('/my/business/products');
          }, 1500); // Show success message for 1.5 seconds before redirecting
          
        } else {
          throw new Error('Product creation failed');
        }
      } catch (error) {
        console.error('Error creating product:', error);
        push.error(error.message || 'Failed to create product. Please try again.');
      }
    },
    addClickOutsideListeners() {
      document.addEventListener('click', this.handleClickOutside);
    },
    removeClickOutsideListeners() {
      document.removeEventListener('click', this.handleClickOutside);
    },
    handleClickOutside(event) {
      if (!event.target.closest('.relative')) {
        this.unitDropdown.isOpen = false;
        this.categoryDropdown.isOpen = false;
      }
    },
    handleUnitFocus() {
      // Always open dropdown on focus to allow searching
      this.unitDropdown.isOpen = true;
      // If no units loaded yet, trigger initial search
      if (this.safeProductUnits.length === 0) {
        this.searchUnits();
      }
    },
    handleCategoryFocus() {
      // Always open dropdown on focus to allow searching
      this.categoryDropdown.isOpen = true;
      // If no categories loaded yet, trigger initial search
      if (this.safeProductCategories.length === 0) {
        this.searchCategories();
      }
    },
    
    // Clear all field-level validation errors
    clearFieldErrors() {
      this.fieldErrors = {
        name: '',
        details: '',
        price: '',
        product_unit_id: '',
        stock: '',
        categories: '',
        images: '',
        specifications: '',
        video_url: ''
      };
    },
    
    // URL validation helper method
    isValidUrl(string) {
      try {
        const url = new URL(string);
        return url.protocol === 'http:' || url.protocol === 'https:';
      } catch (_) {
        return false;
      }
    },
    isValidYouTubeUrl(url) {
      const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/(watch\?v=)?([a-zA-Z0-9_-]{11})/;
      return youtubeRegex.test(url);
    },
    async fetchVideoPreview(url) {
      try {
        const videoId = this.extractVideoId(url);
        if (!videoId) {
          this.videoPreview = null;
          return;
        }

        // Try to fetch from YouTube API if available
        if (import.meta.env.VITE_YOUTUBE_API_KEY) {
          const response = await fetch(`https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=${videoId}&key=${import.meta.env.VITE_YOUTUBE_API_KEY}`);
          const data = await response.json();
          if (data.items && data.items.length > 0) {
            const video = data.items[0];
            this.videoPreview = {
              thumbnail: video.snippet.thumbnails.high.url,
              title: video.snippet.title,
              duration: this.formatDuration(video.contentDetails.duration)
            };
            return;
          }
        }

        // Fallback: Use YouTube thumbnail API
        this.videoPreview = {
          thumbnail: `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`,
          title: 'Video Preview',
          duration: 'Loading...'
        };
      } catch (error) {
        console.error('Error fetching video preview:', error);
        this.videoPreview = null;
      }
    },
    extractVideoId(url) {
      const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
      return match ? match[1] : null;
    },
    formatDuration(duration) {
      const match = duration.match(/PT(\d+H)?(\d+M)?(\d+S)?/);
      let hours = 0;
      let minutes = 0;
      let seconds = 0;

      if (match[1]) hours = parseInt(match[1].replace('H', ''), 10);
      if (match[2]) minutes = parseInt(match[2].replace('M', ''), 10);
      if (match[3]) seconds = parseInt(match[3].replace('S', ''), 10);

      let formattedDuration = '';
      if (hours > 0) {
        formattedDuration += `${hours}h `;
      }
      if (minutes > 0 || hours > 0) {
        formattedDuration += `${minutes}m`;
      }
      if (seconds > 0 || minutes === 0) {
        formattedDuration += `${seconds}s`;
      }
      return formattedDuration.trim();
    },
    
    // Method to format price input to allow up to 2 decimal places
    formatPriceInput(fieldName) {
      let value;
      
      if (fieldName === 'singlePrice') {
        value = this.form.singlePrice;
        if (value !== '') {
          // Format to allow up to 2 decimal places
          const formattedValue = parseFloat(parseFloat(value).toFixed(2));
          if (!isNaN(formattedValue)) {
            this.form.singlePrice = formattedValue;
          } else {
            this.form.singlePrice = '';
          }
        }
      } else if (fieldName.startsWith('priceRange')) {
        if (fieldName === 'priceRange.min') {
          value = this.form.priceRange.min;
          if (value !== '') {
            const formattedValue = parseFloat(parseFloat(value).toFixed(2));
            if (!isNaN(formattedValue)) {
              this.form.priceRange.min = formattedValue;
            } else {
              this.form.priceRange.min = '';
            }
          }
        } else if (fieldName === 'priceRange.max') {
          value = this.form.priceRange.max;
          if (value !== '') {
            const formattedValue = parseFloat(parseFloat(value).toFixed(2));
            if (!isNaN(formattedValue)) {
              this.form.priceRange.max = formattedValue;
            } else {
              this.form.priceRange.max = '';
            }
          }
        }
      }
    }

  }
}
</script>

<style scoped>
/* Fix gradient button colors */
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

.to-green-600 {
  --tw-gradient-to: #059669;
}

.from-green-600 {
  --tw-gradient-from: #059669;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(5, 150, 105, 0));
}

.to-green-700 {
  --tw-gradient-to: #047857;
}

.from-emerald-500 {
  --tw-gradient-from: #10B981;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(16, 185, 129, 0));
}

.to-emerald-600 {
  --tw-gradient-to: #059669;
}

/* Improve dragging experience */
.sortable-ghost {
  opacity: 0.5;
  transform: rotate(5deg);
}

/* Ensure gradient text works properly */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

/* Line clamp utility for video title */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
