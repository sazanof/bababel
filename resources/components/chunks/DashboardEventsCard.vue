<script>
import moment from 'moment'
import MeetingDialogs from './MeetingDialogs.vue'

export default {
    name: 'DashboardEventsCard',
    components: { MeetingDialogs },
    props: {
        card: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            open: false,
            dialogInfoOpen: false,
            dialogMeeting: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        }
    },
    methods: {
        formattedDate(date) {
            return moment(date).format('DD.MM.YYYY HH:mm')
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

<template>
    <VCard
        v-if="card"
        height="340"
        class="d-flex align-center justify-center flex-column">
        <div class="text-center">
            <VCardTitle class="text-h4">
                {{ card.title }}
            </VCardTitle>
            <VCardSubtitle class="text-lg-subtitle-1">
                {{ card.subtitle }}
            </VCardSubtitle>
            <VCardText v-if="card.items.items?.length > 0">
                <VBtn
                    variant="flat"
                    color="deep-orange"
                    :text="$t('{count} meetings', {count: card.items.items?.length})"
                    @click="open = true" />
            </VCardText>
            <VCardText v-else>
                {{ $t('No meetings yet') }}
            </VCardText>
        </div>
        <VDialog
            v-model="open"
            max-width="500">
            <VCard>
                <template #text>
                    <VCardText
                        v-for="meeting in card.items.items"
                        :key="meeting.id">
                        <VChip
                            size="small"
                            rounded="pill"
                            class="mb-2"
                            :color="color(meeting)">
                            {{ status(meeting) }}
                        </VChip>
                        <VChip
                            size="small"
                            rounded="pill"
                            :color="meeting.copied ? 'primary' : 'default'"
                            class="ml-4 mb-2"
                            :prepend-icon="meeting.copied ? 'mdi-check' : 'mdi-link'"
                            @click="copyLink(meeting)">
                            {{ meeting.copied ? $t('Copied') : $t('Copy link') }}
                        </VChip>


                        <div class="datetime blue-grey-lighten-1 font-weight-black">
                            <span>{{ formattedDate(meeting.date) }}</span>
                        </div>
                        <div class="title mb-1">
                            {{ meeting.name }}
                        </div>
                        <div class="organizer">
                            <span class="mr-1 text-caption">{{ $t('Invites you') }}</span>
                            <span class="text-caption font-weight-bold">{{ meeting.owner.lastname }} {{
                                meeting.owner.firstname
                            }}</span>
                        </div>
                        <VChip
                            size="small"
                            class="mt-2"
                            color="deep-orange"
                            rounded="pill"
                            :text="$t('More')"
                            prepend-icon="mdi-chevron-right"
                            @click="onDialogOpen(meeting)" />
                        <VChip
                            size="small"
                            rounded="pill"
                            color="info"
                            class="ml-4 mt-2"
                            prepend-icon="mdi-send"
                            @click="goTo(meeting)">
                            {{ $t('Go') }}
                        </VChip>
                        <VDivider class="mt-4" />
                    </VCardText>
                </template>
            </VCard>
        </VDialog>
        <MeetingDialogs
            v-if="dialogMeeting"
            key="dashboard"
            ref="dashboardDialog"
            :meeting-user="user"
            :show-more="dialogInfoOpen"
            :meeting="dialogMeeting"
            @on-info-change="onInfoDialogClose($event)" />
    </VCard>
</template>

<style scoped lang="scss">

</style>
