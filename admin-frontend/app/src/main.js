import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import router from './router'
import { createNotivue } from 'notivue'
import 'notivue/notification.css' // Only needed if using built-in notifications
import 'notivue/animations.css' // Only needed if using built-in animations

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)
app.use(router)
app.use(createNotivue())
app.mount('#app')
