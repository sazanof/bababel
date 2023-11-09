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
                <v-btn
                    v-if="meeting.status === 1 || meeting.status === 2"
                    prepend-icon="mdi-account-voice"
                    color="deep-orange"
                    class="mt-4"
                    @click="$emit('on-join-click', meeting)">
                    {{ $t('Join') }}
                </v-btn>
            </div>
        </template>
        <template #icon>
            <Avatar
                :size="48"
                :user="meeting.owner" />
        </template>

        <v-card
            :class="{'text-blue-grey-lighten-2': isPast, 'text-grey bg-grey-lighten-4': !isPast && meeting.status === 3}"
            :color="isPast ? 'blue-grey-lighten-5': isUpcoming ? 'yellow-accent-1' : ''">
            <template #title>
                <div class="status mb-1">
                    <v-chip
                        :color="color">
                        {{ status }}
                    </v-chip>
                </div>
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
                    v-if="participantsCount > 0"
                    prepend-icon="mdi-account-multiple"
                    @click="$emit('info-dialog-open', meeting)">
                    {{ $tc('{count} participants', {count: participantsCount}) }}
                </v-btn>
                <v-btn
                    prepend-icon="mdi-eye"
                    @click="$emit('info-dialog-open', meeting)">
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
    </v-timeline-item>
</template>

<script>
import moment from 'moment'
import Avatar from './Avatar.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

export default {
    name: 'MeetingItem',
    components: {
        Avatar
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
    emits: [ 'info-dialog-open', 'info-dialog-close', 'on-join-click' ],
    data() {
        return {
            duplicate: false,
            loading: false,
            showMore: false,
            showPreJoin: false
        }
    },
    computed: {
        settings() {
            return this.$store.getters['getSettings']
        },
        owner() {
            return this.meeting.owner
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
        },
        status() {
            switch (this.meeting.status) {
                case 0:
                    return this.$t('New')
                case 1:
                    return this.$t('Created')
                case 2:
                    return this.$t('Pending')
                case 3:
                    return this.$t('Closed')
                default:
                    return ''
            }
        },
        color() {
            switch (this.meeting.status) {
                case 0:
                    return 'green'
                case 1:
                    return 'deep-orange'
                case 2:
                    return 'info'
                case 3:
                    return 'grey'
                default:
                    return ''
            }
        }
    },
    watch: {},
    methods: {
        preJoinMeeting() {
            this.showPreJoin = true
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
