import '@mdi/font/css/materialdesignicons.css'
import '../scss/app.scss'
import Toast from 'vue-toastification'
import { createVuetify } from 'vuetify'

import '@vuepic/vue-datepicker/dist/main.css'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import router from './router.js'
import store from './store/store'
import { setupI18n, loadLocaleMessages, plural } from './i18n.js'

import App from '../components/App.vue'
import { createApp } from 'vue'

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi
        }
    }
})


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
    app.use(router)
    app.use(vuetify)
    app.use(Toast)
    app.mount('#app')
})
