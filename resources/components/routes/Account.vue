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
                        :user="user"
                        :size="180" />
                    <div class="text-h3 mt-6 mb-4">
                        {{ user.firstname }} {{ user.lastname }}
                    </div>
                    <div class="mb-4 text-grey text-h6">
                        {{ user.position }}, {{ user.department }}
                    </div>
                    <div class="mb-4 text-deep-orange text-h6">
                        {{ user.email }}
                    </div>
                    <input
                        ref="avatarInput"
                        type="file"
                        accept="image/*"
                        class="d-none"
                        @change="openCropper">
                    <v-btn
                        variant="flat"
                        color="deep-orange"
                        prepend-icon="mdi-camera"
                        @click="$refs.avatarInput.click()">
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
        <ImageCropper
            ref="avatarCropper"
            :file="file"
            @on-save="saveAvatar" />
    </v-col>
</template>

<script>
import { useToast } from 'vue-toastification'
import ImageCropper from '../chunks/ImageCropper.vue'
import Avatar from '../chunks/Avatar.vue'

const toast = useToast()
export default {
    name: 'Account',
    components: {
        Avatar,
        ImageCropper
    },

    data() {
        return {
            loading: false,
            notificationsList: null,
            enabledNotifications: null,
            file: new Blob()
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        photo() {
            return this.user.photo ? '/panel/profile/avatar/512' : null
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
                        toast.success(this.$t('Notification enabled'))
                    }).catch(e => {
                    toast.error(e.response.data.message)
                    this.enabledNotifications = this.enabledNotifications.filter(n => {
                        return n !== notification.id
                    })
                })
            } else {
                this.$store.dispatch('deleteNotification', notification.id)
                    .then(() => {
                        toast.success(this.$t('Notification disabled'))
                    })
                    .catch(e => {
                        toast.error(e.response.data.message)
                        this.enabledNotifications = this.enabledNotifications.push(notification.id)
                    })
            }
        },
        openCropper() {
            this.file = this.$refs.avatarInput.files[0]
            this.$refs.avatarCropper.open()
        },
        async saveAvatar({ coordinates, canvas }) {
            await this.$store.dispatch('updateAvatar', {
                coordinates,
                avatar: this.file
            })
            this.$store.commit('updateAvatar', canvas.toDataURL())
        }
    }
}
</script>

<style scoped>

</style>
