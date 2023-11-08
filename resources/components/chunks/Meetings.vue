<template>
    <div class="meetings-wrapper">
        <v-timeline
            v-if="meetings.data"
            class="pa-6"
            align="start"
            side="end">
            <MeetingItem
                v-for="meeting in meetings.data"
                :key="meeting.id"
                :user="user"
                :meeting="meeting" />
        </v-timeline>
    </div>
</template>

<script>
import MeetingItem from './MeetingItem.vue'

export default {
    name: 'Meetings',
    components: {
        MeetingItem
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
    }
}
</script>

<style scoped>

</style>
