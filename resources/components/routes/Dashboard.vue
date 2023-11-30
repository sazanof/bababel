<template>
    <v-col class="dashboard">
        <v-row>
            <v-col
                v-for="card in cards"
                :key="card"
                cols="12">
                <v-card
                    width="100%"
                    :loading="loading"
                    :title="card.title"
                    :subtitle="card.subtitle">
                    <v-list width="100%">
                        <template
                            v-for="meeting in card.items.items"
                            :key="meeting.id">
                            <v-list-item lines="three">
                                <v-list-item-title>
                                    <v-chip
                                        class="mb-2"
                                        :color="color(meeting)">
                                        {{ status(meeting) }}
                                    </v-chip>
                                    <v-chip
                                        :color="meeting.copied ? 'primary' : 'default'"
                                        class="ml-4 mb-2"
                                        :prepend-icon="meeting.copied ? 'mdi-check' : 'mdi-link'"
                                        @click="copyLink(meeting)">
                                        {{ meeting.copied ? $t('Copied') : $t('Copy link') }}
                                    </v-chip>
                                    <v-chip
                                        color="info"
                                        class="ml-4 mb-2"
                                        prepend-icon="mdi-send"
                                        @click="goTo(meeting)">
                                        {{ $t('Go') }}
                                    </v-chip>
                                    <div class="datetime blue-grey-lighten-1 font-weight-black">
                                        <span>{{ formattedDate(meeting.date) }}</span>
                                    </div>
                                    <div class="title mb-1">
                                        {{ meeting.name }}
                                    </div>
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    <div class="subtitle mb-2">
                                        {{ meeting.welcome }}
                                    </div>
                                </v-list-item-subtitle>
                                <div class="organizer">
                                    <span class="mr-1 text-caption">{{ $t('Invites you') }}</span>
                                    <span class="text-caption font-weight-bold">{{ meeting.owner.lastname }} {{
                                        meeting.owner.firstname
                                    }}</span>
                                </div>
                                <template #append>
                                    <v-btn
                                        variant="text"
                                        icon="mdi-chevron-right"
                                        @click="onDialogOpen(meeting)" />
                                </template>
                            </v-list-item>

                            <v-divider class="my-2" />
                        </template>
                    </v-list>
                    <v-card-actions>
                        <v-btn>{{ $t('More') }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

        <MeetingDialogs
            v-if="dialogMeeting"
            key="dashboard"
            ref="dashboardDialog"
            :user="user"
            :show-more="dialogInfoOpen"
            :meeting="dialogMeeting"
            @on-info-change="onInfoDialogClose($event)" />
    </v-col>
</template>

<script>
import moment from 'moment'
import MeetingDialogs from '../chunks/MeetingDialogs.vue'

const m = moment
export default {
    name: 'Dashboard',
    components: { MeetingDialogs },
    data() {
        return {
            copied: false,
            dialogMeeting: null,
            dialogInfoOpen: false,
            loading: false,
            cards: [
                {
                    title: this.$t('Agenda'),
                    subtitle: this.$t('Meetings scheduled for today'),
                    items: []
                },
                {
                    title: this.$t('Upcoming meetings'),
                    subtitle: this.$t('Upcoming meetings in which you take part'),
                    items: []
                }
            ],
            recent: null,
            today: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        }
    },
    async created() {
        this.loading = true
        const all = await this.$store.dispatch('getDashboardMeetings')
            .catch(() => {

            })
            .finally(() => {
                this.loading = false
            })
        if (all) {
            this.today = all.today
            this.recent = all.recent
            this.cards[0].items = this.today
            this.cards[1].items = this.recent
        }
    },
    methods: {
        async copyLink(m) {
            try {
                const link = `${document.location.protocol}//${document.location.host}/#/meetings/${m.id}/view`
                await navigator.clipboard.writeText(link)
                m.copied = true
                setTimeout(() => {
                    m.copied = false
                }, 1500)

            } catch (e) {
                console.error(e)
            }
        },
        goTo(m) {
            this.$router.push({
                name: 'meeting_page', params: {
                    id: m.id
                }
            })
        },
        formattedDate(date) {
            return m(date).format('DD.MM.YYYY HH:mm')
        },
        status(meeting) {
            switch (meeting.status) {
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
        color(meeting) {
            switch (meeting.status) {
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
        },
        onDialogOpen(meeting) {
            console.log('open')
            this.dialogMeeting = meeting
            this.$nextTick(() => {
                this.dialogInfoOpen = true
            })
        },
        async onInfoDialogClose(state) {
            if (state === false) {
                setTimeout(() => {
                    this.dialogInfoOpen = false
                    this.dialogMeeting = null
                }, 400)
            } else {
                await this.$store.dispatch('getMeetingInfo', this.dialogMeeting.id)
                    .catch(e => {
                        alert(e.response.data.message)
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.title {
    font-weight: bold;
    color: var(--color-orange);
}

.datetime {
    font-size: 12px;
    display: flex;
    align-items: center;
}

.organizer {
    font-weight: bold;
}
</style>
