<template>
    <div
        v-if="loaded"
        id="conference-wrapper"
        class="container">
        <div
            v-if="($route.name === 'meeting_page' || $route.name === 'meeting_logout_page') && !authenticated"
            class="app-wrapper single-meeting-view">
            <router-view />
        </div>
        <div
            v-else
            class="app-wrapper">
            <Login v-if="!authenticated" />
            <Index
                v-else
                :user="user" />
        </div>
        <AppNotifications />
    </div>
</template>

<script>
import Index from './pages/Index.vue'
import Login from './pages/Login.vue'
import AppNotifications from './chunks/AppNotifications.vue'

export default {
    name: 'App',
    components: {
        AppNotifications,
        Login,
        Index
    },
    data() {
        return {
            loaded: false
        }
    },
    computed: {
        authenticated() {
            return this.$store.getters['isAuthenticated']
        },
        user() {
            return this.$store.getters['getUser']
        }
    },
    async created() {
        window.top.postMessage({ fromIFrame: true })
        await this.checkAuth().finally(() => {
            this.loaded = true
        })
    },
    methods: {
        async checkAuth() {
            await this.$store.dispatch('checkAuth')
        }
    }
}
</script>

<style scoped>

</style>
