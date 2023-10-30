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
                            v-model="name"
                            prepend-icon="mdi-text-shadow"
                            class="mb-6"
                            hide-details
                            :label="$t('Meeting name')" />
                        <v-select
                            v-model="meetingLayout"
                            :label="$t('Style')"
                            :items="layoutOptions"
                            prepend-icon="mdi-palette-swatch-variant"
                            :item-props="true" />
                        <vue-date-picker v-model="date">
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
                            v-model="welcome"
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

                        <UsersSearch @update:participants="participants = $event" />
                    </v-col>
                    <v-col cols="4">
                        <div
                            class="text-subtitle-1 text-grey-darken-1 text-button mb-4">
                            {{ $t('Advanced settings') }}
                            <v-divider />
                        </div>
                        <v-switch
                            v-model="record"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Record')" />
                        <v-switch
                            v-if="record"
                            v-model="autoStartRecording"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Auto start recording')" />
                        <v-switch
                            v-model="webcamsOnlyForModerator"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Webcams only for moderator')" />
                        <v-switch
                            v-model="allowModsToEjectCameras"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Managing participant cameras')" />
                        <v-switch
                            v-model="muteOnStart"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Mute on start')" />
                        <v-switch
                            v-model="lockSettingsDisableMic"
                            color="deep-orange"
                            :value="true"
                            :label="$t('All participants are listeners')" />
                        <v-switch
                            v-model="allowModsToUnmuteUsers	"
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
                            prepend-icon="mdi-close">
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
            loading: false,
            date: new Date().toISOString(),
            caption: null,
            name: null,
            welcome: null,
            record: true,
            autoStartRecording: false,
            webcamsOnlyForModerator: false,
            muteOnStart: false,
            lockSettingsDisableMic: false,
            allowModsToUnmuteUsers: true,
            allowModsToEjectCameras: true,
            participants: [],
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
            meetingLayout: null,
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
        }
    },
    watch: {
        record() {
            if (this.record === false && this.autoStartRecording === true) {
                this.autoStartRecording = false
            }
        }
    },
    created() {
        this.caption = this.$t('New meeting')
        this.meetingLayout = {
            title: this.$t('Smart'),
            value: 'SMART_LAYOUT'
        }
    },
    methods: {
        async saveMeeting(event) {
            this.loading = true
            // todo post data
            const data = {
                id: this.id,
                userId: parseInt(this.user.id),
                date: this.date,
                name: this.name,
                welcome: this.welcome,
                record: this.record,
                autoStartRecording: this.autoStartRecording,
                webcamsOnlyForModerator: this.webcamsOnlyForModerator,
                muteOnStart: this.muteOnStart,
                lockSettingsDisableMic: this.lockSettingsDisableMic,
                allowModsToUnmuteUsers: this.allowModsToUnmuteUsers,
                allowModsToEjectCameras: this.allowModsToEjectCameras,
                meetingLayout: this.meetingLayout.value,
                participants: this.participants.map(p => {
                    return {
                        id: p.id,
                        isModerator: p.isModerator ?? false
                    }
                }),
                files: this.files
            }
            console.log(data)

            if (this.id === null) {
                this.$store.dispatch('addMeeting', data).catch(e => {
                    toast.error(e.response.data.message)
                }).finally(() => {
                    this.loading = false
                })
            } else {
                this.$store.dispatch('editMeeting', data).catch(e => {
                    toast.error(e.response.data.message)
                }).finally(() => {
                    this.loading = false
                })
            }


        },
        addFiles(e) {
            this.files = e
        }
    }

}
</script>

<style scoped lang="scss">

</style>
