/**
 * Image validation utility functions
 * Validates images according to the backend requirements:
 * 'required|image|mimes:jpeg,jpg,png|max:5120'
 */

/**
 * Validates a file object according to backend requirements
 * @param {File} file - The file to validate
 * @returns {Object} - Validation result with isValid boolean and error message
 */
import { push } from "notivue";
export function validateImageFile(file) {
  // Check if file exists
  if (!file) {
    push.warning('Image is required');
    return {
      isValid: false,
      error: 'Image is required'
    };
  }

  // Check if it's an image
  if (!file.type.startsWith('image/')) {
    push.warning('File must be an image');
    return {
      isValid: false,
      error: 'File must be an image'
    };
  }

  // Check file extension
  const allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
  const fileExtension = file.name.split('.').pop().toLowerCase();
  
  if (!allowedExtensions.includes(fileExtension)) {
    push.warning('Only JPEG, JPG, PNG, and WEBP files are allowed');
    return {
      isValid: false,
      error: 'Only JPEG, JPG, PNG, and WEBP files are allowed'
    };
  }

  // Check file size (5MB = 5 * 1024 * 1024 bytes)
  const maxSize = 2 * 1024 * 1024;
  if (file.size > maxSize) {
    push.warning('File size must be less than 2MB');
    return {
      isValid: false,
      error: 'File size must be less than 2MB'
    };
  }

  return {
    isValid: true,
    error: null
  };
}

/**
 * Validates multiple image files
 * @param {File[]} files - Array of files to validate
 * @returns {Object} - Validation result with isValid boolean and error message
 */
export function validateMultipleImages(files) {
  if (!Array.isArray(files) || files.length === 0) {
    push.warning('At least one image is required');
    return {
      isValid: false,
      error: 'At least one image is required'
    };
  }

  for (let i = 0; i < files.length; i++) {
    const validation = validateImageFile(files[i]);
    if (!validation.isValid) {
      push.warning(`Image ${i + 1}: ${validation.error}`);
      return {
        isValid: false,
        error: `Image ${i + 1}: ${validation.error}`
      };
    }
  }

  return {
    isValid: true,
    error: null
  };
}

/**
 * Creates FormData for single image upload
 * @param {File} file - The image file to upload
 * @param {string} fieldName - The field name for the image (default: 'image')
 * @returns {FormData} - FormData object ready for upload
 */
export function createImageFormData(file, fieldName = 'image') {
  const formData = new FormData();
  formData.append(fieldName, file);
  return formData;
}

/**
 * Creates FormData for multiple image uploads
 * @param {File[]} files - Array of image files to upload
 * @param {string} fieldName - The field name for the images (default: 'images')
 * @returns {FormData} - FormData object ready for upload
 */
export function createMultipleImagesFormData(files, fieldName = 'images') {
  const formData = new FormData();
  files.forEach((file, index) => {
    formData.append(`${fieldName}[${index}]`, file);
  });
  return formData;
}

/**
 * Creates FormData for product image upload
 * @param {File} file - The image file to upload
 * @param {number} productId - The product ID
 * @returns {FormData} - FormData object ready for product image upload
 */
export function createProductImageFormData(file, productId) {
  const formData = new FormData();
  formData.append('image', file);
  formData.append('product_id', productId);
  return formData;
}

/**
 * Creates FormData for business profile update
 * @param {File} file - The profile image file
 * @returns {FormData} - FormData object ready for business profile update
 */
export function createBusinessProfileFormData(file) {
  const formData = new FormData();
  formData.append('business_profile', file);
  return formData;
}

/**
 * Creates FormData for user profile update
 * @param {File} file - The profile image file
 * @returns {FormData} - FormData object ready for user profile update
 */
export function createUserProfileFormData(file) {
  const formData = new FormData();
  formData.append('profile', file);
  return formData;
}

/**
 * Creates FormData for identity verification documents
 * @param {File} nidFront - Front side of NID document
 * @param {File} nidBack - Back side of NID document
 * @param {string} nidNumber - NID number
 * @returns {FormData} - FormData object ready for identity verification
 */
export function createIdentityVerificationFormData(nidFront, nidBack, nidNumber) {
  const formData = new FormData();
  if (nidFront) formData.append('nid_front', nidFront);
  if (nidBack) formData.append('nid_back', nidBack);
  formData.append('nid_number', nidNumber);
  return formData;
}

/**
 * Creates FormData for review submission with images
 * @param {Object} reviewData - Review data object
 * @param {File[]} images - Array of image files
 * @returns {FormData} - FormData object ready for review submission
 */
export function createReviewFormData(reviewData, images = []) {
  const formData = new FormData();
  
  // Add review data
  Object.keys(reviewData).forEach(key => {
    if (key !== 'images') {
      formData.append(key, reviewData[key]);
    }
  });
  
  // Add images
  images.forEach((image, index) => {
    if (image instanceof File) {
      formData.append(`images[${index}]`, image);
    }
  });
  
  return formData;
}
