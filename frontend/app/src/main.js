import { createApp } from 'vue'
import { createHead } from '@unhead/vue'
import './assets/fonts/fonts.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import { createNotivue } from 'notivue'
import 'notivue/notification.css' // Only needed if using built-in notifications
import 'notivue/animations.css' // Only needed if using built-in animations
import './style.css'
import App from './App.vue'
import router from './router'
import store from './store'
import { configure, defineRule } from 'vee-validate';
import { required, email, url, min, max, numeric, regex } from '@vee-validate/rules';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';

const app = createApp(App)
const head = createHead()

app.use(head)

// Make Yup globally available
app.config.globalProperties.$yup = yup;
// Now you can use this.$yup in any component

// Register vee-validate components globally
app.component('Form', Form);
app.component('Field', Field);

app.use(store).use(router).use(createNotivue())

// Define global rules
defineRule('required', required);
defineRule('email', email);
defineRule('url', url);
defineRule('min', min);
defineRule('max', max);
defineRule('numeric', numeric);
defineRule('regex', regex);

// Define custom rule for Bangladesh mobile numbers
defineRule('bd_mobile', (value) => {
  if (!value) return 'Mobile number is required';
  if (!/^01[3-9][0-9]{8}$/.test(value)) {
    return 'Please enter a valid Bangladesh mobile number (e.g., 019192123..)';
  }
  return true;
});

// Define custom rule for alpha spaces (letters and spaces only)
defineRule('alpha_spaces', (value) => {
  if (!value) return true; // Let required rule handle empty values
  if (!/^[a-zA-Z\s]+$/.test(value)) {
    return 'This field can only contain letters and spaces';
  }
  return true;
});

// Define custom rule for min_value validation
defineRule('min_value', (value, [min]) => {
  if (!value) return true; // Let required rule handle empty values
  const numValue = Number(value);
  if (isNaN(numValue) || numValue < min) {
    return `Value must be at least ${min}`;
  }
  return true;
});

// Define custom rule for max_value validation
defineRule('max_value', (value, [max]) => {
  if (!value) return true; // Let required rule handle empty values
  const numValue = Number(value);
  if (isNaN(numValue) || numValue > max) {
    return `Value must be at most ${max}`;
  }
  return true;
});

// Configure vee-validate
configure({
  validateOnInput: true,
  validateOnBlur: true,
  validateOnChange: true,
  validateOnModelUpdate: true,
})

// Initialize authentication state on app startup
store.dispatch('initAuth').then(() => {
  app.mount('#app')
}).catch(error => {
  console.error('Failed to initialize authentication:', error)
  app.mount('#app')
})