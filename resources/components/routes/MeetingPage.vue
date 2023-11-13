<template>
    <div class="meeting-page">
        <v-sheet
            v-if="meeting"
            elevation="12"
            max-width="800"
            rounded="lg"
            width="100%"
            class="pa-4 text-center mx-auto meeting-view">
            <div class="img">
                <div
                    v-if="loading"
                    class="loading">
                    <v-progress-circular
                        :size="140"
                        :width="6"
                        indeterminate
                        color="white">
                        <template #default>
                            <div
                                v-if="user ===null"
                                class="icon-empty">
                                <v-icon
                                    color="blue-grey-darken-1"
                                    icon="mdi-account"
                                    :size="100" />
                            </div>
                            <Avatar
                                v-else
                                :user="user"
                                :size="130" />
                        </template>
                    </v-progress-circular>
                </div>
                <div
                    v-else
                    class="join-info">
                    <div class="icon-empty">
                        <v-icon
                            v-if="user === null"
                            color="blue-grey-darken-1"
                            icon="mdi-account"
                            :size="100" />
                        <Avatar
                            v-else
                            :user="user"
                            :size="130" />
                    </div>
                    <v-text-field
                        v-model="fullName"
                        class="mt-4 w-75"
                        :label="$t('Name')"
                        variant="solo" />
                </div>
            </div>
            <h2 class="text-h5 mb-6">
                {{ meeting.meeting.name }}
            </h2>

            <p class="mb-4 text-medium-emphasis text-body-2">
                {{ meeting.meeting.welcome }}
            </p>
            <v-divider class="mb-4" />
            <v-btn
                v-if="canJoin && !loading"
                :disabled="(fullName === null || fullName.length < 3) || joinLoader"
                :loading="joinLoader"
                color="deep-orange"
                prepend-icon="mdi-send"
                @click="joinMeeting">
                {{ $t('Join') }}
            </v-btn>
            <v-btn
                v-if="canStart"
                color="deep-orange"
                prepend-icon="mdi-send"
                @click="startMeeting">
                {{ $t('Start') }}
            </v-btn>
            <v-bottom-sheet
                v-if="loading">
                <span class="mt-2 ml-4 text-button text-deep-orange">{{ $t('Waiting for connection') }}</span>
            </v-bottom-sheet>
        </v-sheet>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'

const toast = useToast()
import Avatar from '../chunks/Avatar.vue'

export default {
    name: 'MeetingPage',
    components: {
        Avatar
    },
    data() {
        return {
            loading: true,
            joinLoader: false,
            meeting: null,
            refreshHandle: null,
            fullName: null
        }
    },
    computed: {
        id() {
            return this.$route.params.id
        },
        user() {
            return this.$store.getters['getUser']
        },
        isOwner() {
            return this.meeting.isOwner
        },
        isParticipant() {
            return this.meeting.isParticipant
        },
        isModerator() {
            return this.meeting.isModerator
        },
        canJoin() {
            return this.meeting.canJoin
        },
        canStart() {
            return this.meeting.meeting.status === 0 && (this.isOwner || this.isModerator)
        },
        authenticated() {
            return this.$store.getters['isAuthenticated']
        }
    },
    async created() {
        if (this.user !== null) {
            await this.$store.dispatch('getMeetingInfo', this.id).catch(e => {
                console.log(e)
            })
        }

        if (this.user !== null) {
            this.fullName = `${this.user?.lastname} ${this.user?.firstname}`
        }
        await this.viewMeeting()
        this.refreshHandle = setInterval(async () => {
            await this.viewMeeting()
            if (this.meeting.canJoin) {
                clearInterval(this.refreshHandle)
                this.loading = false
            }
        }, 3000)
    },
    methods: {
        async viewMeeting() {
            if (this.id > 0) {
                this.meeting = await this.$store.dispatch('viewMeeting', this.id)
            }
        },
        async startMeeting() {
            await this.$store.dispatch('startMeeting', this.id).then(() => {
                this.loading = false
                this.joinLoader = false
            })
        },
        async joinMeeting() {
            this.joinLoader = true
            const method = this.user !== null ? 'joinMeeting' : 'joinMeetingAsGuest'
            const joinInfo = await this.$store.dispatch(method, {
                id: this.id,
                visibleName: this.fullName
            })
                .catch(e => {
                    this.duplicate = true
                    this.link = e.response.data.link
                    toast.error(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                    this.joinLoader = false
                })
            if (joinInfo) {
                const url = joinInfo.join.url
                window.open(url, '_blank').focus()
            }
        }
    }
}
</script>

<style scoped lang="scss">
.meeting-page {
    background: rgba(0, 0, 0, 0.4);
    position: fixed;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;

    &:before {
        content: "";
        position: fixed;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        z-index: 2;
        background: url("/resources/img/meeting-bg.jpg") center center;
        background-size: cover;
        opacity: 0.4;
        filter: blur(15px);
    }

    .meeting-view {
        max-width: 800px;
        width: 90%;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        z-index: 10;

    }

    .custom-card-title {
        background-size: cover;
    }

    .join-info {
        width: 100%;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-items: center;
    }

    .img {
        margin: -16px -16px 10px -16px;
        height: 300px;
        background: url("/resources/img/meeting-bg.jpg") center center;
        background-size: cover;
        border-radius: 6px 6px 0 0;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;

        .icon-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
        }
    }
}
</style>
