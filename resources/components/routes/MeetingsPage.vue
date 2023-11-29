<template>
    <v-col>
        <v-card
            :loading="loading"
            :title="title"
            :subtitle="criteriaText">
            <Meetings
                v-if="meetings"
                :user="user"
                :meetings="meetings" />
        </v-card>
    </v-col>
</template>

<script>
import { useToast } from 'vue-toastification'
import Meetings from '../chunks/Meetings.vue'

const toast = useToast()

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
            loading: false,
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
        },
        criteriaText() {
            switch (this.criteria) {
                case 'my':
                    return this.$t('New and failed meetings organized by you are shown here')
                case 'invitations':
                    return this.$t('New and failed meetings to which you were invited are shown here')
                case 'past':
                    return this.$t('Past meetings are displayed here')
                case 'records':
                    return this.$t('Meetings that have been recorded are displayed here')
                default:
                    return null
            }
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
            this.loading = true
            await this.$store.dispatch('getMeetings', this.filter)
                .catch((e) => {
                    toast.error(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                })
        }
    }
}
</script>

<style scoped>

</style>
