<template>
    <VMenu
        v-model="menu"
        :close-on-content-click="false"
        location="end">
        <template #activator="{ props }">
            <VBtn
                class="mr-2"
                icon="mdi-magnify"
                v-bind="props" />
        </template>

        <VCard
            width="500px"
            max-height="500px">
            <template #text>
                <VTextField
                    v-model="term"
                    :label="$t('Search meeting')"
                    :loading="loading"
                    density="compact"
                    variant="outlined"
                    prepend-inner-icon="mdi-magnify"
                    @keydown="loading = true">
                    <template #append-inner>
                        <VBtn
                            v-if="term"
                            density="compact"
                            variant="text"
                            icon="mdi-close"
                            @click="term = null" />
                    </template>
                </VTextField>
                <VList v-if="meetings">
                    <VListItem
                        v-for="m in meetings"
                        :key="m.id"
                        @click="goToMeeting(m)">
                        <template #title>
                            {{ m.name }}
                        </template>
                        <template #subtitle>
                            {{ toDate(m.date) }}, ({{ m?.owner?.lastname }} {{ m?.owner?.firstname }})
                        </template>
                    </VListItem>
                </VList>
            </template>
        </VCard>
    </VMenu>
</template>

<script>
import { formatDate } from '../../js/helpers/formatDate.js'
import debounce from '../../js/helpers/debounce.js'

export default {
    name: 'TopSearch',
    data() {
        return {
            loading: false,
            menu: null,
            term: null,
            debounceSearch: null,
            meetings: null,
            meeting: null

        }
    },
    watch: {
        term() {
            this.debounceSearch()
        }
    },
    created() {
        this.debounceSearch = debounce(this.search, 500)
    },
    methods: {
        async search() {
            if (this.term?.length > 0) {
                this.meetings = await this.$store.dispatch('searchMeetings', this.term).finally(() => {
                    this.loading = false
                })
            }
        },
        toDate(d) {
            return formatDate(d)
        },
        goToMeeting(m) {
            this.term = null
            this.loading = false
            this.menu = null
            this.meetings = null
            this.$router.push({
                name: 'meeting_page', params: {
                    id: m.id
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
