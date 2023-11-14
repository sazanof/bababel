<template>
    <v-col cols="12">
        <v-card
            v-if="user !== null"
            :loading="loading"
            class="account">
            <v-card-title>
                {{ $t('Account settings') }}
            </v-card-title>
            <v-card-text>
                <div class="user-profile text-center mt-4">
                    <Avatar
                        v-if="!user.photo"
                        :user="user"
                        :size="180" />
                    <div class="text-h3 mt-6 mb-4">
                        {{ user.firstname }} {{ user.lastname }}
                    </div>
                    <div class="mb-4 text-grey text-h6">
                        {{ user.position }}, {{ user.department }}
                    </div>
                    <v-btn
                        variant="flat"
                        color="deep-orange"
                        prepend-icon="mdi-camera">
                        {{ $t('Change photo') }}
                    </v-btn>
                    <v-divider class="mt-4" />
                    <div class="notifications">
                        <div class="text-h5 pt-4">
                            {{ $t('Notifications settings') }}
                        </div>
                    </div>
                    <v-sheet
                        max-width="300"
                        class="ma-auto">
                        <v-switch
                            v-for="n in notificationsList"
                            :key="n.id"
                            v-model="enabledNotifications"
                            color="deep-orange"
                            :value="n.id"
                            :label="n.title"
                            @update:modelValue="updateNotificationSetting(n, $event)" />
                    </v-sheet>
                </div>
            </v-card-text>
        </v-card>
    </v-col>
</template>

<script>
import { useToast } from 'vue-toastification'
import Avatar from '../chunks/Avatar.vue'

const toast = useToast()
export default {
    name: 'Account',
    components: {
        Avatar
    },

    data() {
        return {
            loading: false,
            notificationsList: null,
            enabledNotifications: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        }
    },
    async created() {
        await this.getNotifications()
    },
    methods: {
        async getNotifications() {
            const notifications = await this.$store.dispatch('getNotificationSettings')
            if (notifications) {
                this.notificationsList = notifications.list
                this.enabledNotifications = notifications.enabled
            }
        },
        async updateNotificationSetting(notification, val) {
            console.log(notification, val)
            if (val.indexOf(notification.id) !== -1) {
                this.$store.dispatch('addNotification', notification.id)
                    .then(() => {
                        toast.success('Notification enabled')
                    }).catch(e => {
                    toast.error(e.response.data.message)
                    this.enabledNotifications = this.enabledNotifications.filter(n => {
                        return n !== notification.id
                    })
                })
            } else {
                this.$store.dispatch('deleteNotification', notification.id)
                    .then(() => {
                        toast.success('Notification disabled')
                    })
                    .catch(e => {
                        toast.error(e.response.data.message)
                        this.enabledNotifications = this.enabledNotifications.push(notification.id)
                    })
            }
        }
    }
}
</script>

<style scoped>

</style>
