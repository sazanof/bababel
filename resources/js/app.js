import './bootstrap'
import '../scss/app.scss'

import store from './store/store.js'
import { setupI18n, loadLocaleMessages, plural } from './i18n.js'

import App from '../components/App.vue'
import { createApp } from 'vue'
const i18n = setupI18n({
    locale: 'ru',
    pluralizationRules: {
        ru: plural
    }
})
loadLocaleMessages(i18n, i18n.global.locale).then(() => {
    const app = createApp(App)
    app.use(store)
    app.use(i18n)
    app.mount('#app')
})
