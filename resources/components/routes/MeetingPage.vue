<template>
    <div
        v-if="!authenticated"
        class="meeting-page">
        <VSheet
            v-if="meeting && hasAccessToJoinMeeting"
            elevation="12"
            max-width="800"
            rounded="lg"
            width="100%"
            class="pa-4 text-center mx-auto meeting-view">
            <div class="img" :style="`background-image: url('${background}')`">
                <div
                    v-if="loading"
                    class="loading">
                    <VProgressCircular
                        :size="140"
                        :width="6"
                        indeterminate
                        color="white">
                        <template #default>
                            <div
                                v-if="user ===null"
                                class="icon-empty">
                                <VIcon
                                    color="blue-grey-darken-1"
                                    icon="mdi-account"
                                    :size="100"/>
                            </div>
                            <Avatar
                                v-else
                                :user="user"
                                :size="130"/>
                        </template>
                    </VProgressCircular>
                </div>
                <div
                    v-else
                    class="join-info">
                    <div class="icon-empty">
                        <VIcon
                            v-if="user === null"
                            color="blue-grey-darken-1"
                            icon="mdi-account"
                            :size="100"/>
                        <Avatar
                            v-else
                            :user="user"
                            :size="130"/>
                    </div>
                    <VTextField
                        v-model="fullName"
                        class="mt-4 w-75"
                        :label="$t('Name')"
                        variant="solo"/>
                </div>
            </div>
            <VAlert
                v-if="time <=0 && isStarted && !loading"
                color="success"
                class="my-2">
                {{ $t('The meeting has begun. You can join') }}
            </VAlert>
            <VAlert
                v-else-if="time <=0 && !isStarted"
                color="primary"
                class="my-2">
                {{ $t('Wait for the organizer to start the meeting') }}
            </VAlert>
            <h2 class="text-h6 text-deep-orange my-4">
                {{ date }}
            </h2>
            <VueCountdown
                v-if="time > 0"
                v-slot="{ days, hours, minutes, seconds }"
                :time="time"
                :interval="100">
                {{ $t('Meeting will begin') }}
                <div class="count-wrapper d-flex align-center justify-center">
                    <div class="count text-white bg-deep-orange ma-3">
                        {{ days }}
                        <span class="text-grey">{{ $t('{count} days', {count: days}) }}</span>
                    </div>
                    <div class="count text-white bg-deep-orange ma-3">
                        {{ hours }}
                        <span class="text-grey">{{ $t('{count} hours', {count: hours}) }}</span>
                    </div>
                    <div class="count text-white bg-deep-orange ma-3">
                        {{ minutes }}
                        <span class="text-grey">{{ $t('{count} minutes', {count: minutes}) }}</span>
                    </div>
                    <div class="count text-white bg-deep-orange ma-3">
                        {{ seconds }}
                        <span class="text-grey">{{ $t('{count} seconds', {count: seconds}) }}</span>
                    </div>
                </div>
            </VueCountdown>
            <h2 class="text-h5 mb-6">
                {{ meeting.meeting.name }}
            </h2>

            <p class="mb-4 text-medium-emphasis text-body-2">
                {{ meeting.meeting.welcome }}
            </p>
            <a
                v-if="url"
                :href="url"
                class="redirect-link text-deep-orange"
                target="_blank">
                {{ $t('If you have not been redirected, click here') }}
            </a>
            <VDivider class="mb-4"/>
            <VBtn
                v-if="canJoin && !loading"
                :disabled="(fullName === null || fullName.length < 3) || joinLoader"
                :loading="joinLoader"
                color="deep-orange"
                prepend-icon="mdi-send"
                @click="joinMeeting">
                {{ $t('Join') }}
            </VBtn>
            <div
                v-if="loading"
                class="loading">
                <span class="mt-2 ml-4 text-button text-deep-orange">{{ $t('Waiting for connection') }}</span>
            </div>
        </VSheet>
        <VSheet
            v-else
            elevation="12"
            max-width="800"
            rounded="lg"
            width="100%"
            class="pa-4 text-center mx-auto meeting-view">
            {{ $t('You have not access to meeting') }}
        </VSheet>
    </div>
    <!-- IF USER AUTHENTICATED -->
    <VCol v-else>
        <VCard
            v-if="meeting"
            width="100%"
            class="text-no-wrap pa-2"
            :loading="loading">
            <MessageDialog
                v-if="isGuest && !isModerator && !isParticipant"
                :model="isGuest === true"
                :title="$t('You are not invited to this meeting')"
                :text="$t('You are not a participant in this meeting, but you can join it because it has guest access enabled')"
                :btn-text="$t('I am understand')"/>
            <MessageDialog
                v-else-if="!isGuest && !isModerator && !isParticipant"
                :model="!isGuest"
                :title="$t('You are not invited to this meeting')"
                :text="$t('You are not a participant in this meeting')"
                btn-icon="mdi-emoticon-sad-outline"
                :btn-text="$t('Very sad')"/>
            <MessageDialog
                v-else-if="!isStarted && !isModerator"
                :model="!isStarted && !isModerator"
                :title="$t('Meeting not running')"
                :text="$t('Administrator has not started the meeting yet. Please wait until it starts, and then you can join')"
                btn-icon="mdi-check"
                :btn-text="$t('OK')"/>
            <MessageDialog
                v-if="hasAccessToJoinMeeting"
                :model="!hasAccessToJoinMeeting"
                :title="$t('You have not access to meeting')"
                :text="$t('You do not have access to this meeting. Contact the organizer')"
                btn-icon="mdi-check"
                :btn-text="$t('OK')"/>
            <template #title>
                <VChip
                    class="mr-2"
                    color="blue"
                    prepend-icon="mdi-clock">
                    {{ date }}
                </VChip>
                <VChip
                    color="primary"
                    prepend-icon="mdi-account"
                    @click="showOwner = !showOwner">
                    {{
                        $t('Organizer: {lastname} {firstname}', {
                            lastname: meeting.meeting?.owner?.lastname,
                            firstname: meeting.meeting?.owner?.firstname
                        })
                    }}
                </VChip>
                <VSheet
                    v-if="showOwner"
                    class="user pa-2 my-4"
                    rounded="lg"
                    color="blue-grey-lighten-5">
                    <MeetingOwner :user="meeting.meeting.owner"/>
                </VSheet>
                <div class="title mt-2">
                    {{ meeting.meeting.name }}
                </div>
            </template>
            <template #subtitle>
                {{ meeting.meeting.welcome }}
            </template>
            <VCardText>
                <div
                    v-if="loading"
                    class="card-countdown-wrapper">
                    <VueCountdown
                        v-if="time > 0"
                        v-slot="{ days, hours, minutes, seconds }"
                        :time="time"
                        :interval="100">
                        {{ $t('Meeting will begin') }}
                        <div class="card-counter text-button">
                            <div class="card-counter-item">
                                {{ days }}
                                <span>{{ $t('{count} days', {count: days}) }}</span>
                            </div>
                            <div class="card-counter-item">
                                {{ hours }}
                                <span>{{ $t('{count} hours', {count: hours}) }}</span>
                            </div>
                            <div class="card-counter-item">
                                {{ minutes }}
                                <span>{{ $t('{count} minutes', {count: minutes}) }}</span>
                            </div>
                            <div class="card-counter-item">
                                {{ seconds }}
                                <span>{{ $t('{count} seconds', {count: seconds}) }}</span>
                            </div>
                        </div>
                    </VueCountdown>
                </div>
                <div
                    v-else
                    class="join-info">
                    <VTextField
                        v-model="fullName"
                        :label="$t('Name')"/>
                </div>
                <a
                    v-if="url"
                    :href="url"
                    class="redirect-link"
                    target="_blank">
                    {{ $t('If you have not been redirected, click here') }}
                </a>
            </VCardText>
            <VCardActions>
                <VBtn
                    v-if="canJoin || (!loading && isStarted)"
                    :disabled="(fullName === null || fullName.length < 3) || joinLoader"
                    :loading="joinLoader"
                    color="deep-orange"
                    prepend-icon="mdi-send"
                    @click="joinMeeting">
                    {{ $t('Join') }}
                </VBtn>
                <VBtn
                    v-if="canStart"
                    color="deep-orange"
                    prepend-icon="mdi-send"
                    @click="startMeeting">
                    {{ $t('Start') }}
                </VBtn>
            </VCardActions>
        </VCard>
    </VCol>
</template>

<script>
import VueCountdown from '@chenfengyuan/vue-countdown'
import MessageDialog from '../chunks/MessageDialog.vue'
import moment from 'moment'
import MeetingOwner from '../chunks/MeetingOwner.vue'

import Avatar from '../chunks/Avatar.vue'
import {createErrorNotification} from '../../js/helpers/notifications.js'

import * as logo from '/resources/img/lot.png';
import * as bg from '/resources/img/meeting-bg.jpg';

export default {
    name: 'MeetingPage',
    components: {
        Avatar,
        VueCountdown,
        MessageDialog,
        MeetingOwner
    },
    data() {
        return {
            loading: true,
            joinLoader: false,
            meeting: null,
            refreshHandle: null,
            fullName: null,
            url: null,
            showOwner: false
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.id)
        },
        background() {
            if (this.id === 369) {
                return logo.default
            }
            return bg.default
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
        isGuest() {
            return this.meeting.isGuest
        },
        isStarted() {
            return this.meeting?.isStarted
        },
        canStart() {
            return (this.meeting.meeting.status === 0 || this.meeting.meeting.status === 3) && (this.isOwner || this.isModerator)
        },
        authenticated() {
            return this.$store.getters['isAuthenticated']
        },
        date() {
            return moment(this.meeting.meeting.date).format('DD.MM.YYYY HH:mm')
        },
        time() {
            const time = moment(this.meeting.meeting.date).toDate() - new Date()
            return time < 0 ? 0 : time
        },
        hasAccessToJoinMeeting() {
            return this.meeting !== null && (this.isGuest || this.isOwner || this.isModerator || this.isParticipant)
        }
    },
    async mounted() {
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
    unmounted() {
        clearInterval(this.refreshHandle)
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
                this.meeting.canJoin = true
                this.meeting.isStarted = true
                this.meeting.meeting.status = 1
                this.joinMeeting()
            })
        },
        async joinMeeting() {
            this.joinLoader = true
            const method = this.user !== null ? 'joinMeeting' : 'joinMeetingAsGuest'
            const _window = this.user !== null ? null : window.open()
            const joinInfo = await this.$store.dispatch(method, {
                id: this.id,
                visibleName: this.fullName
            })
                .catch(e => {
                    if (e.response.status === 500) {
                        this.$store.commit('addNotification', createErrorNotification(e.response.data.message))
                    } else {
                        this.duplicate = true
                        this.link = e.response.data.link
                        if (this.user !== null && this.authenticated) {
                            this.$store.commit('setActiveJoinInfo', e.response.data)
                            this.$router.push({
                                name: 'bbb',
                                params: {
                                    id: this.id,
                                    pid: e.response.data.join.id
                                }
                            })
                        }
                    }

                })
                .finally(() => {
                    this.loading = false
                    this.joinLoader = false
                })
            if (joinInfo && !this.duplicate) {
                this.url = joinInfo.join.url
                if (this.user !== null) {
                    this.$store.commit('setActiveJoinInfo', joinInfo)
                    this.$router.push({
                        name: 'bbb',
                        params: {
                            id: this.id,
                            pid: joinInfo.join.pid
                        }
                    })
                } else {
                    if (_window !== null) {
                        _window.location = joinInfo.join.url
                    }
                    //window.open(joinInfo.join.url, '_blank').focus()
                }
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

    .redirect-link {
        text-align: center;
        display: block;
        margin: 10px 0;
    }

    &:before {
        content: "";
        position: fixed;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        z-index: 2;
        background-size: cover;
        background: url("/resources/img/meeting-bg.jpg") center center;
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
        background-size: cover;
        background-position: top center;
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

.count-wrapper {
    margin-bottom: 34px;

    .count {
        width: 40px;
        height: 40px;
        display: flex;
        font-size: 18px;
        font-weight: bold;
        border-radius: 6px;
        justify-content: center;
        align-items: center;
        position: relative;

        span {
            position: absolute;
            bottom: -26px;
            font-size: 14px;
            font-weight: normal;
        }
    }
}

.card-counter {
    display: flex;
    align-items: center;

    .card-counter-item {
        margin-right: 6px;
    }
}
</style>
