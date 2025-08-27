<template>
    <VCol class="dashboard">
        <VRow class="fill-height">
            <VCol
                cols="12"
                md="6">
                <VCard
                    hover
                    height="340"
                    class="d-flex align-center justify-center card-bg"
                    cover
                    variant="tonal">
                    <VCardText
                        class="text-center position-relative"
                        style="z-index:100">
                        <VBtn
                            variant="flat"
                            color="deep-orange"
                            size="x-large"
                            prepend-icon="mdi-plus"
                            :text="$t('Create meeting')"
                            @click="openCreateMeeting" />
                    </VCardText>
                </VCard>
            </VCol>
            <VCol
                cols="12"
                md="6">
                <VCard
                    height="340"
                    class="fill-height"
                    variant="tonal"
                    :title="$t('Meeting records')">
                    <template #text>
                        <VList height="270">
                            <VListItem
                                v-for="record in records.items"
                                :key="record.id"
                                :title="record.meeting.name">
                                <template #subtitle>
                                    <VIcon
                                        :="32"
                                        color="blue-grey-lighten-3"
                                        icon="mdi-timer-outline" />
                                    {{ record.created_at }}
                                </template>
                                <template #append>
                                    <VBtn
                                        icon="mdi-play"
                                        variant="text"
                                        density="comfortable"
                                        :href="record.url"
                                        target="_blank" />
                                </template>
                            </VListItem>
                        </VList>
                    </template>
                </VCard>
            </VCol>
            <VCol
                v-for="card in cards"
                :key="card"
                class="fill-height"
                cols="12"
                md="6">
                <VCard
                    v-if="card"
                    height="340"
                    class="d-flex align-center justify-center flex-column"
                    :loading="loading">
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
                                :text="$t('{count} meetings', {count: card.items.items?.length})" />
                        </VCardText>
                    </div>
                    <!--                    <VCarousel-->
                    <!--                        :cycle="true"-->
                    <!--                        :continuous="true"-->
                    <!--                        width="100%">-->
                    <!--                        <VCarouselItem-->
                    <!--                            v-for="meeting in card.items.items"-->
                    <!--                            :key="meeting.id">-->
                    <!--                            <VCard-->
                    <!--                                variant="text"-->
                    <!--                                :subtitle>-->
                    <!--                                <VChip-->
                    <!--                                    class="mb-2"-->
                    <!--                                    :color="color(meeting)">-->
                    <!--                                    {{ status(meeting) }}-->
                    <!--                                </VChip>-->
                    <!--                                <VChip-->
                    <!--                                    :color="meeting.copied ? 'primary' : 'default'"-->
                    <!--                                    class="ml-4 mb-2"-->
                    <!--                                    :prepend-icon="meeting.copied ? 'mdi-check' : 'mdi-link'"-->
                    <!--                                    @click="copyLink(meeting)">-->
                    <!--                                    {{ meeting.copied ? $t('Copied') : $t('Copy link') }}-->
                    <!--                                </VChip>-->
                    <!--                                <VChip-->
                    <!--                                    color="info"-->
                    <!--                                    class="ml-4 mb-2"-->
                    <!--                                    prepend-icon="mdi-send"-->
                    <!--                                    @click="goTo(meeting)">-->
                    <!--                                    {{ $t('Go') }}-->
                    <!--                                </VChip>-->
                    <!--                                <div class="datetime blue-grey-lighten-1 font-weight-black">-->
                    <!--                                    <span>{{ formattedDate(meeting.date) }}</span>-->
                    <!--                                </div>-->
                    <!--                                <div class="title mb-1">-->
                    <!--                                    {{ meeting.name }}-->
                    <!--                                </div>-->
                    <!--                            </VCard>-->
                    <!--                            <div class="organizer">-->
                    <!--                                <span class="mr-1 text-caption">{{ $t('Invites you') }}</span>-->
                    <!--                                <span class="text-caption font-weight-bold">{{ meeting.owner.lastname }} {{-->
                    <!--                                    meeting.owner.firstname-->
                    <!--                                }}</span>-->
                    <!--                            </div>-->
                    <!--                            <template #append>-->
                    <!--                                <VBtn-->
                    <!--                                    variant="text"-->
                    <!--                                    icon="mdi-chevron-right"-->
                    <!--                                    @click="onDialogOpen(meeting)" />-->
                    <!--                            </template>-->
                    <!--                        </VCarouselItem>-->
                    <!--                    </VCarousel>-->
                </VCard>
            </VCol>
        </VRow>


        <MeetingDialogs
            v-if="dialogMeeting"
            key="dashboard"
            ref="dashboardDialog"
            :user="user"
            :show-more="dialogInfoOpen"
            :meeting="dialogMeeting"
            @on-info-change="onInfoDialogClose($event)" />
    </VCol>
</template>

<script>
import moment from 'moment'
import MeetingDialogs from '../chunks/MeetingDialogs.vue'
import RecordItem from '../chunks/RecordItem.vue'

const m = moment
export default {
    name: 'Dashboard',
    components: { RecordItem, MeetingDialogs },
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
            recent: [],
            today: [],
            records: []
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
            this.records = all.records
            this.cards[0].items = this.today
            this.cards[1].items = this.recent
        }
    },
    methods: {
        openCreateMeeting() {
            this.$store.commit('clearMeetingState')
            this.$router.push({ name: 'create_meeting' })
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
.card-bg {
    &:after {
        content: "";
        position: absolute;
        top: -10px;
        right: -10px;
        left: -10px;
        bottom: -10px;
        z-index: 11;
        background-image: url("../../img/meeting-bg.jpg");
        background-position: center;
        background-size: cover;
        overflow: hidden;
        opacity: 0.7;
    }
}

.dashboard {
    height: 100%;
    max-height: 800px;
    display: flex;
    align-items: center;
    justify-content: center;
}

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
