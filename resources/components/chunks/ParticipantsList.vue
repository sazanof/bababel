<template>
    <v-row class="mt-3">
        <ParticipantItem
            v-for="participant in participants"
            :key="participant.id"
            :meeting="meeting"
            :user="participant" />
    </v-row>
</template>

<script>
import ParticipantItem from './ParticipantItem.vue'

export default {
    name: 'ParticipantsList',
    components: { ParticipantItem },
    props: {
        participants: {
            type: Object,
            required: true
        },
        meeting: {
            type: Object,
            required: true
        }
    }
}
</script>

<style lang="scss" scoped>
.participants-list {
    display: flex;
    flex-wrap: wrap;
}
</style>
