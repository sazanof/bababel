<template>
    <v-col>
        <v-form @submit.prevent="saveMeeting">
            <v-card
                :title="caption">
                <v-row class="px-4">
                    <v-col class="mr-4">
                        <div
                            class="text-subtitle-1 text-grey-darken-1 text-button mb-4">
                            {{ $t('Meeting parameters') }}
                            <v-divider />
                        </div>
                        <v-text-field
                            v-model="meeting.name"
                            prepend-icon="mdi-text-shadow"
                            class="mb-6"
                            hide-details
                            :label="$t('Meeting name')" />
                        <v-select
                            v-model="meeting.layout"
                            :label="$t('Style')"
                            :items="layoutOptions"
                            prepend-icon="mdi-palette-swatch-variant"
                            :item-props="true" />
                        <vue-date-picker
                            v-model="date"
                            @update:model-value="meeting.date = formattedDate">
                            <template #trigger>
                                <v-text-field
                                    v-model="formattedDate"
                                    hide-details
                                    class="mb-6"
                                    :label="$t('Date')"
                                    prepend-icon="mdi-calendar" />
                            </template>
                        </vue-date-picker>
                        <v-textarea
                            v-model="meeting.welcome"
                            prepend-icon="mdi-text-long"
                            class="mb-6"
                            hide-details
                            :label="$t('Welcome message or description')" />

                        <v-file-input
                            class="mb-6"
                            hide-details
                            prepend-icon="mdi-paperclip"
                            :chips="true"
                            :multiple="true"
                            :label="$t('Files')"
                            @update:modelValue="addFiles" />

                        <UsersSearch
                            :participants="meeting.participants"
                            @update:participants="meeting.participants = $event" />
                    </v-col>
                    <v-col cols="4">
                        <div
                            class="text-subtitle-1 text-grey-darken-1 text-button mb-4">
                            {{ $t('Advanced settings') }}
                            <v-divider />
                        </div>
                        <v-switch
                            v-model="meeting.record"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Record')" />
                        <v-switch
                            v-if="meeting.record"
                            v-model="meeting.autoStartRecording"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Auto start recording')" />
                        <v-switch
                            v-model="meeting.webcamsOnlyForModerator"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Webcams only for moderator')" />
                        <v-switch
                            v-model="meeting.allowModsToEjectCameras"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Managing participant cameras')" />
                        <v-switch
                            v-model="meeting.muteOnStart"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Mute on start')" />
                        <v-switch
                            v-model="meeting.lockSettingsDisableMic"
                            color="deep-orange"
                            :value="true"
                            :label="$t('All participants are listeners')" />
                        <v-switch
                            v-model="meeting.allowModsToUnmuteUsers	"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Managing participant microphones')" />
                    </v-col>
                </v-row>
                <v-row class="pb-4 px-4">
                    <v-col>
                        <v-btn
                            :loading="loading"
                            :disabled="loading"
                            type="submit"
                            class="mr-4"
                            prepend-icon="mdi-content-save"
                            color="deep-orange">
                            {{ $t('Save') }}
                        </v-btn>
                        <v-btn
                            :disabled="loading"
                            prepend-icon="mdi-close"
                            @click="cancelCreateMeeting">
                            {{ $t('Cancel') }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card>
        </v-form>
    </v-col>
</template>

<script>
import { useToast } from 'vue-toastification'
import UsersSearch from '../chunks/UsersSearch.vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import moment from 'moment'

const m = moment
const toast = useToast()

export default {
    name: 'CreateMeeting',
    components: {
        VueDatePicker,
        UsersSearch
    },
    data() {
        return {
            date: null,
            loading: false,
            caption: null,
            files: null,
            layoutOptions: [
                {
                    title: this.$t('Custom'),
                    value: 'CUSTOM_LAYOUT'
                },
                {
                    title: this.$t('Smart'),
                    value: 'SMART_LAYOUT'
                },
                {
                    title: this.$t('Presentation focus'),
                    value: 'PRESENTATION_FOCUS'
                },
                {
                    title: this.$t('Video focus'),
                    value: 'VIDEO_FOCUS'
                }
            ],
            query: null,
            showDateMenu: false
        }
    },
    computed: {
        id() {
            return this.$route.params.id ?? null
        },
        formattedDate() {
            return this.date ? m(this.date).format('DD.MM.YYYY HH:mm') : m().format('DD.MM.YYYY HH:mm')
        },
        user() {
            return this.$store.getters['getUser']
        },
        meeting() {
            return this.$store.getters['getMeeting']
        },
        record() {
            return this.meeting.record
        }
    },
    watch: {
        id() {
            this.meeting.id = this.id
        },
        record() {
            if (this.meeting.record === false && this.meeting.autoStartRecording === true) {
                this.meeting.autoStartRecording = false
            }
        }
    },
    async created() {
        this.meeting.id = this.id
        if (this.meeting.id > 0) {
            await this.getMeeting().catch(e => {
                alert('Error getting meeting')
            })
        } else {
            this.$store.commit('clearMeetingState')
        }
        this.date = this.meeting.date
        this.meeting.layout = {
            title: this.$t('Smart'),
            value: 'SMART_LAYOUT'
        }

    },
    methods: {
        async saveMeeting(event) {
            this.loading = true
            const data = this.meeting.toObject()
            if (this.id === null) {
                this.$store.dispatch('addMeeting', data)
                    .then(() => {
                        this.$router.push({ name: 'meetings.my' })
                    })
                    .catch(e => {
                        toast.error(e.response.data.message)
                    })
                    .finally(() => {
                        this.loading = false
                    })
            } else {
                this.$store.dispatch('editMeeting', data)
                    .then(() => {
                        toast.success(this.$t('Meeting saved'))
                    })
                    .catch(e => {
                        toast.error(e.response.data.message)
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }


        },
        addFiles(e) {
            this.meeting.files = e
        },
        async getMeeting() {
            await this.$store.dispatch('getMeeting', this.id)
        },
        cancelCreateMeeting() {
            this.$store.commit('clearMeetingState')
            this.$router.push('/meetings')
        }
    }

}
</script>

<style scoped lang="scss">

</style>
