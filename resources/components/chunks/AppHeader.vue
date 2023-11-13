<template>
    <v-app-bar
        class="bg-blue-grey-lighten-5"
        :elevation="0">
        <template #append>
            <v-btn icon="mdi-magnify" />
            <v-btn
                icon="mdi-account"
                @click="account" />
            <v-btn
                icon="mdi-logout"
                :disabled="loading.logout"
                :loading="loading.logout"
                @click="logout" />
        </template>
        <template #prepend>
            <v-btn
                icon="mdi-menu"
                variant="text"
                @click="$emit('on-menu-click', $event)" />
        </template>
    </v-app-bar>
</template>

<script>
export default {
    name: 'AppHeader',
    emits: [ 'on-menu-click' ],
    data() {
        return {
            loading: {
                logout: false,
                search: false
            }
        }
    },
    methods: {
        logout() {
            this.loading.logout = true
            this.$store.dispatch('logOut').finally(() => {
                this.loading.logout = false
            })
        },
        account() {
            this.$router.push({ name: 'account' })
        }
    }
}
</script>

<style scoped>

</style>
