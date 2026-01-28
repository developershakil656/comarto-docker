import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'

export function useFormValidation(schema) {
  const { handleSubmit, errors, resetForm, setErrors } = useForm({
    validationSchema: schema
  })

  return {
    handleSubmit,
    errors,
    resetForm,
    setErrors
  }
}

export function useFieldValidation(name, rules) {
  const { value, errorMessage, handleBlur, handleChange } = useField(name, rules)
  
  return {
    value,
    errorMessage,
    handleBlur,
    handleChange
  }
}

// Common validation schemas
export const validationSchemas = {
  required: (fieldName) => `${fieldName} is required`,
  email: 'Please enter a valid email address',
  minLength: (min) => `Must be at least ${min} characters`,
  maxLength: (max) => `Must not exceed ${max} characters`,
  phone: 'Please enter a valid phone number',
  url: 'Please enter a valid URL'
}

// Yup schema helpers
export const createSchema = (fields) => {
  return yup.object(fields)
}
