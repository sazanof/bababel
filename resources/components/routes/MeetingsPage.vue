<template>
    <v-col>
        <v-card
            :title="title"
            :subtitle="$t('New and failed meetings organized by you are shown here')">
            <Meetings
                v-if="meetings"
                :user="user"
                :meetings="meetings" />
        </v-card>
    </v-col>
</template>

<script>
import Meetings from '../chunks/Meetings.vue'

export default {
    name: 'MeetingsPage',
    components: {
        Meetings
    },
    props: {
        criteria: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            filter: {
                criteria: this.criteria,
                page: 1,
                limit: 50
            }
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        title() {
            switch (this.criteria) {
                case 'invitations':
                    return this.$t('Invitations')
                case 'past':
                    return this.$t('Past meetings')
                default :
                    return this.$t('My meetings')
            }
        },
        meetings() {
            return this.$store.getters['getMeetings']
        }
    },
    watch: {
        async criteria() {
            this.filter.criteria = this.criteria
            this.$store.commit('setMeetings', {})
            this.$store.commit('clearMeetingState')
            await this.getMeetings()
        }
    },
    async created() {
        await this.getMeetings()
    },
    methods: {
        async getMeetings() {
            await this.$store.dispatch('getMeetings', this.filter)
        }
    }
}
</script>

<style scoped>

</style>
