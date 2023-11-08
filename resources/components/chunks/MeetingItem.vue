<template>
    <v-timeline-item>
        <template #opposite>
            <div class="info">
                <div class="user text-button">
                    {{ owner.lastname }} {{ owner.firstname }}
                </div>
                <div class="position text-caption text-grey-darken-1">
                    {{ owner.position }}, {{ owner.department }}
                </div>
            </div>
        </template>
        <template #icon>
            <Avatar
                :size="48"
                :user="meeting.owner" />
        </template>

        <v-card
            :class="isPast ? 'text-blue-grey-lighten-2' : ''"
            :color="isPast ? 'blue-grey-lighten-5': isUpcoming ? 'yellow-accent-1' : ''">
            <template #title>
                <div
                    v-if="isPast"
                    class="is-past text-caption">
                    {{ $t('The meeting did not take place') }}
                </div>
                {{ meeting.name }}
            </template>
            <v-card-subtitle>
                <div class="text-overline date">
                    <v-icon icon="mdi-clock" />
                    <span>{{ dateMod }}</span>
                </div>
            </v-card-subtitle>
            <v-card-text>
                <p>
                    {{ meeting.welcome }}
                </p>
            </v-card-text>
            <v-card-actions>
                <v-btn
                    v-if="meeting.status === 1 || meeting.status === 2"
                    :disabled="loading"
                    prepend-icon="mdi-account-voice"
                    color="deep-orange"
                    variant="flat"
                    :text="$t('Join')"
                    @click="preJoinMeeting" />
                <v-btn
                    v-if="participantsCount > 0"
                    prepend-icon="mdi-account-multiple"
                    @click="showMore = true">
                    {{ $tc('{count} participants', {count: participantsCount}) }}
                </v-btn>
                <v-btn
                    prepend-icon="mdi-eye"
                    @click="showMore = true">
                    {{ $t('More') }}
                </v-btn>
                <v-btn
                    v-if="isOwner"
                    prepend-icon="mdi-pencil"
                    @click="$router.push(`/meetings/${meeting.id}`)">
                    {{ $t('Edit') }}
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog
            v-model="showMore"
            transition="dialog-bottom-transition">
            <template #default="{ isActive }">
                <v-card>
                    <v-card-title>{{ meeting.name }}</v-card-title>
                    <v-card-text class="pa-4">
                        {{ meeting.welcome }}
                        <ParticipantsList
                            :meeting="meeting"
                            :participants="participants" />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer />
                        <div class="pa-4 d-flex justify-space-between">
                            <div>
                                <v-btn
                                    v-if="meeting.status === 0 || meeting.status === 3"
                                    :disabled="loading"
                                    prepend-icon="mdi-play"
                                    color="deep-orange"
                                    variant="flat"
                                    :text="$t('Start')"
                                    @click="startMeeting" />
                                <v-btn
                                    v-else-if="meeting.status === 1 || meeting.status === 2"
                                    :disabled="loading"
                                    prepend-icon="mdi-account-voice"
                                    color="deep-orange"
                                    variant="flat"
                                    :text="$t('Join')"
                                    @click="preJoinMeeting" />
                                <v-btn
                                    v-if="meeting.status === 2"
                                    :disabled="loading"
                                    prepend-icon="mdi-stop"
                                    color="primary"
                                    variant="flat"
                                    :text="$t('Stop')"
                                    @click="stopMeeting" />
                                <v-btn
                                    v-if="meeting.status !== 2"
                                    :disabled="loading"
                                    prepend-icon="mdi-trash-can"
                                    color="red"
                                    variant="flat"
                                    :text="$t('Delete')"
                                    @click="deleteMeeting" />
                            </div>
                            <div>
                                <v-btn
                                    :disabled="loading"
                                    class="ml-2"
                                    prepend-icon="mdi-close"
                                    :text="$t('Close')"
                                    @click="isActive.value = false" />
                            </div>
                        </div>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
        <v-dialog
            v-model="showPreJoin"
            width="400">
            <template #default="{isActive}">
                <v-card :title="$t('Join to meeting')">
                    <v-card-text>
                        <v-text-field
                            v-model="visibleName"
                            :label="$t('Enter visible name')" />
                        <v-btn
                            v-if="link && duplicate"
                            color="deep-orange"
                            prepend-icon="mdi-link"
                            width="100%"
                            :href="link"
                            target="_blank">
                            {{ $t('Previous link') }}
                        </v-btn>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer />
                        <v-btn
                            color="deep-orange"
                            variant="flat"
                            :loading="joinLoader"
                            :disabled="joinLoader"
                            prepend-icon="mdi-check"
                            :text="$t('Join')"
                            @click="joinMeeting" />
                        <v-btn
                            prepend-icon="mdi-close"
                            :text="$t('Cancel')"
                            @click="isActive.value = false" />
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </v-timeline-item>
</template>

<script>
import moment from 'moment'
import ParticipantsList from './ParticipantsList.vue'
import Avatar from './Avatar.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

export default {
    name: 'MeetingItem',
    components: {
        Avatar,
        ParticipantsList
    },
    props: {
        meeting: {
            type: Object,
            required: true
        },
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            link: null,
            duplicate: false,
            joinLoader: false,
            loading: false,
            showMore: false,
            showPreJoin: false,
            visibleName: null
        }
    },
    computed: {
        settings() {
            return this.$store.getters['getSettings']
        },
        owner() {
            return this.meeting.owner
        },
        currentParticipant() {
            return this.meeting.participants.find(p => {
                return p.id === this.user.id
            })
        },
        isOwner() {
            return this.user.id === this.meeting.userId
        },
        participants() {
            return this.meeting.participants
        },
        participantsCount() {
            return this.participants?.length
        },
        dateMod() {
            return moment(this.meeting.date).format(this.format)
        },
        format() {
            return `${this.settings.dateFormat} ${this.settings.timeFormat}`
        },
        isPast() {
            return moment(new Date()).isAfter(this.meeting.date) && this.meeting.status === 0
        },
        isUpcoming() {
            const date = this.meeting.date
            const now = new Date()
            const min = moment(now).subtract(8, 'hours').toDate()
            const max = moment(now).add(8, 'hours').toDate()
            return moment(now).isBetween(min, new Date(date)) && moment(date).isBefore(max)
        }
    },
    watch: {
        async showMore() {
            if (this.showMore) {
                await this.$store.dispatch('getMeetingInfo', this.meeting.id)
                    .catch(e => {
                        alert(e.response.data.message)
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        }
    },
    created() {
        this.visibleName = `${this.user.lastname} ${this.user.firstname}`
    },
    methods: {
        async startMeeting() {
            this.loading = true
            await this.$store.dispatch('startMeeting', this.meeting.id)
                .catch(e => {
                    alert(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                })
        },
        preJoinMeeting() {
            this.showPreJoin = true
        },
        async joinMeeting() {
            this.duplicate = false
            this.link = null
            this.loading = true
            this.joinLoader = true
            const joinInfo = await this.$store.dispatch('joinMeeting', {
                id: this.meeting.id,
                visibleName: this.visibleName
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
                this.showPreJoin = false
                const url = joinInfo.join.url
                window.open(url, '_blank').focus()
            }
        },
        async stopMeeting() {
            this.loading = true
            await this.$store.dispatch('stopMeeting', this.meeting.id)
                .then(() => {
                    this.joinLoader = true
                })
                .catch(e => {
                    alert(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteMeeting() {

        }
    }
}
</script>

<style lang="scss" scoped>
.info {
    text-align: right;
    line-height: 16px;
    max-width: 230px;

    .user {
        font-weight: bold;
        line-height: 16px;
    }
}

.date {
    line-height: 16px;
    font-weight: bold;
    display: flex;
    align-items: center;

    span {
        display: inline-block;
        margin-left: 6px;
        position: relative;
        top: 1px
    }
}

.is-past {
    font-style: italic;
}
</style>
