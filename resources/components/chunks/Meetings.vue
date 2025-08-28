<template>
    <div class="meetings-wrapper">
        <VTimeline
            v-if="meetings.data"
            class="pa-6"
            align="start"
            side="end">
            <MeetingItem
                v-for="meeting in meetings.data"
                :key="meeting.id"
                :meeting-user="user"
                :meeting="meeting"
                @on-join-click="onJoinClick($event)"
                @info-dialog-open="onInfoDialogOpen($event)" />
        </VTimeline>
        <MeetingDialogs
            v-if="dialogMeeting"
            ref="meetingDialogs"
            :meeting-user="user"
            :meeting="dialogMeeting"
            :show-more="dialogInfoOpen"
            @on-join-success="onJoinSuccess"
            @on-info-change="onInfoDialogClose($event)" />
    </div>
</template>

<script>
import MeetingDialogs from './MeetingDialogs.vue'
import MeetingItem from './MeetingItem.vue'

export default {
    name: 'Meetings',
    components: {
        MeetingItem,
        MeetingDialogs
    },
    props: {
        meetings: {
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
            dialogMeeting: null,
            dialogInfoOpen: false
        }
    },
    methods: {
        onInfoDialogOpen(meeting) {
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
        },
        onJoinSuccess({ url, duplicate }) {
            this.dialogMeeting.status = 2 // PENDING STATUS
            console.log('You joined to meeting', url, duplicate)
        },
        onJoinClick(meeting) {
            this.dialogMeeting = meeting
            this.$nextTick(() => {
                this.$refs.meetingDialogs.join()
            })

        }
    }
}
</script>

<style scoped>

</style>
