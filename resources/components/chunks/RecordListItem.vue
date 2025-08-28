<script>
import moment from 'moment'
import { formatTime } from 'vuetify/lib/util/index.js'

export default {
    name: 'RecordListItem',
    props: {
        record: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            m: moment
        }
    },
    computed: {
        duration() {
            return formatTime(this.record.processingTime ?? 0)
        }
    }
}
</script>

<template>
    <VListItem
        :key="record.id"
        :title="record.meeting.name">
        <template #subtitle>
            <VChip
                size="small"
                density="comfortable"
                readonly
                rounded="pill"
                :text="m(record.startAt).format('DD.MM.YYYY HH:mm')"
                prepend-icon="mdi-calendar" />
            <VChip
                size="small"
                class="ml-2"
                density="comfortable"
                readonly
                rounded="pill"
                :text="`${duration}`"
                prepend-icon="mdi-timer-outline" />
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
</template>

<style scoped lang="scss">

</style>
