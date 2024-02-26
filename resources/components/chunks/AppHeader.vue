<template>
    <v-app-bar
        class="bg-blue-grey-lighten-5"
        :elevation="0">
        <template #append>
            <v-tooltip :text="$t('Service running in development mode')">
                <template #activator="{ props }">
                    <v-btn
                        variant="tonal"
                        v-bind="props"
                        icon="mdi-alert"
                        color="error" />
                </template>
            </v-tooltip>
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
                :icon="rail ? 'mdi-menu-close' : 'mdi-menu-open'"
                variant="text"
                @click="$emit('on-menu-click', $event)" />
        </template>
        <ConfirmationDialog ref="confirmLogout" />
    </v-app-bar>
</template>

<script>
import ConfirmationDialog from './ConfirmationDialog.vue'

export default {
    name: 'AppHeader',
    components: {
        ConfirmationDialog
    },
    emits: [ 'on-menu-click' ],
    data() {
        return {
            loading: {
                logout: false,
                search: false
            }
        }
    },
    computed: {
        rail() {
            return this.$store.getters['getRail']
        }
    },
    methods: {
        async logout() {
            const ok = await this.$refs.confirmLogout.show({
                okColor: 'success',
                okIcon: 'mdi-logout',
                title: this.$t('Logout confirmation'),
                message: this.$t('If there are active meetings in the system, they will be completed'),
                okButton: this.$t('Exit')
            })
            if (ok) {
                this.loading.logout = true
                await this.$store.dispatch('logOut').finally(() => {
                    this.loading.logout = false
                })
            }

        },
        account() {
            this.$router.push({ name: 'account' })
        }
    }
}
</script>

<style scoped>

</style>
