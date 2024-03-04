<template>
    <v-app-bar
        class="bg-blue-grey-lighten-5"
        :elevation="0">
        <template #append>
            <v-tooltip :text="$t('Leave request')">
                <template #activator="{ props }">
                    <v-btn
                        class="mr-2"
                        v-bind="props"
                        icon="mdi-bullhorn-variant"
                        color="success"
                        @click="openRequestWindow" />
                </template>
            </v-tooltip>
            <v-tooltip :text="$t('Service running in development mode')">
                <template #activator="{ props }">
                    <v-btn
                        class="mr-2"
                        v-bind="props"
                        icon="mdi-alert"
                        color="error" />
                </template>
            </v-tooltip>
            <TopSearch />
            <v-btn
                class="mr-2"
                icon="mdi-account"
                @click="account" />
            <v-btn
                class="mr-2"
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
        <AppDialog
            ref="leaveRequest"
            :title="$t('Leave request')"
            :subtitle="$t('To send suggestions and register errors when working in the system')">
            <VForm>
                <VTextField
                    v-model="subject"
                    class="mb-4"
                    :loading="loading.feedback"
                    :label="$t('Subject')" />
                <VTextarea
                    v-model="message"
                    :loading="loading.feedback"
                    :label="$t('Message')" />
                <VFileInput
                    class="mb-4"
                    :loading="loading.feedback"
                    :label="$t('Files')"
                    type="file"
                    :chips="true"
                    accept="image/*"
                    :multiple="true"
                    @update:model-value="files = $event" />
                <VBtn
                    :loading="loading.feedback"
                    :disabled="disabled"
                    color="deep-orange"
                    class="w-100"
                    prepend-icon="mdi-send"
                    @click="leaveRequest">
                    {{ $t('Send') }}
                </VBtn>
            </VForm>
        </AppDialog>
        <ConfirmationDialog ref="confirmLogout" />
    </v-app-bar>
</template>

<script>
import { useToast } from 'vue-toastification'
import AppDialog from './AppDialog.vue'
import ConfirmationDialog from './ConfirmationDialog.vue'
import TopSearch from './TopSearch.vue'

const toast = useToast()

export default {
    name: 'AppHeader',
    components: {
        TopSearch,
        ConfirmationDialog,
        AppDialog
    },
    emits: [ 'on-menu-click' ],
    data() {
        return {
            loading: {
                logout: false,
                search: false,
                feedback: false
            },
            subject: '',
            message: '',
            files: null,
            feedbackSend: false
        }
    },
    computed: {
        rail() {
            return this.$store.getters['getRail']
        },
        disabled() {
            return this.subject.length < 3 || this.message.length < 3 || this.loading.feedback
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
        },
        openRequestWindow() {
            this.$refs.leaveRequest.show()
        },
        async leaveRequest() {
            this.loading.feedback = true
            await this.$store.dispatch('leaveRequest', {
                subject: this.subject,
                message: this.message,
                files: this.files
            }).then(() => {
                this.feedbackSend = true
                this.subject = ''
                this.message = ''
                this.files = null
                this.$refs.leaveRequest.close()
                toast.success(this.$t('Feedback send'))
            }).catch(() => {
                toast.error(this.$t('Error sending feedback'))
            }).finally(() => {
                this.loading.feedback = false
            })
        }
    }
}
</script>

<style scoped>

</style>
