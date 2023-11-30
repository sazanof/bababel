<template>
    <v-list-item
        :value="record"
        lines="three"
        class="record pa-4">
        <template #title>
            {{ meeting.name }}
        </template>
        <template #subtitle>
            {{ moment(record.startTime).format(format) }} - {{ moment(record.endTime).format(format) }}
        </template>
        <template #prepend>
            <div class="duration d-flex flex-column justify-center align-center mr-8">
                <v-icon
                    :size="32"
                    color="blue-grey-lighten-3"
                    icon="mdi-timer-outline" />
                <div class="text-caption font-weight-bold text-blue-grey-darken-1 mt-2">
                    {{ duration.hours }}:{{ duration.minutes }}:{{ duration.seconds }}
                </div>
            </div>
        </template>
        <template #append>
            <v-btn
                class="mr-2"
                icon="mdi-play"
                variant="text"
                color="blue-grey"
                @click="openLink" />
            <v-btn
                icon="mdi-trash-can"
                variant="text"
                color="red"
                @click="deleteRecord" />
        </template>
        <ConfirmationDialog
            ref="deleteRecord"
            color="error" />
    </v-list-item>
</template>

<script>
import ConfirmationDialog from './ConfirmationDialog.vue'
import moment from 'moment'

const m = moment
export default {
    name: 'RecordItem',
    components: {
        ConfirmationDialog
    },
    props: {
        meeting: {
            type: Object,
            required: true
        },
        record: {
            type: Object,
            required: true
        }
    },
    computed: {
        moment() {
            return m
        },
        settings() {
            return this.$store.getters['getSettings']
        },
        format() {
            return `${this.settings.dateFormat} ${this.settings.timeFormat}`
        },
        duration() {
            const t = this.moment(this.record.endTime).toDate().getTime() - this.moment(this.record.startTime).toDate().getTime()
            return this.msToTime(t)
        },
        isOwner() {
            return this.user.id === this.meeting.userId
        }
    },
    methods: {
        msToTime(duration) {
            let milliseconds = Math.floor((duration % 1000) / 100),
                seconds = Math.floor((duration / 1000) % 60),
                minutes = Math.floor((duration / (1000 * 60)) % 60),
                hours = Math.floor((duration / (1000 * 60 * 60)) % 24)
            hours = (hours < 10) ? '0' + hours : hours
            minutes = (minutes < 10) ? '0' + minutes : minutes
            seconds = (seconds < 10) ? '0' + seconds : seconds
            return { hours, minutes, seconds, milliseconds }
        },
        openLink() {
            window.open(this.record.url, '_blank').focus()
        },
        async deleteRecord() {
            const ok = await this.$refs.deleteRecord.show({
                okColor: 'white',
                okIcon: 'mdi-trash-can',
                title: this.$t('Delete this record?'),
                message: this.$t('Sure delete record?'),
                okButton: this.$t('Delete')
            })
        }
    }
}
</script>

<style scoped>

</style>
