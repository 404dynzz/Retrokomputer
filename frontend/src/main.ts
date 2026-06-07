import './assets/main.css'
import { useTheme } from './utils/theme'

const { initTheme } = useTheme()
initTheme()

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
