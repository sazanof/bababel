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
                            v-model="layout"
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
                            hide-details
                            prepend-icon="mdi-paperclip"
                            :chips="true"
                            :multiple="true"
                            :label="$t('Files')"
                            @update:modelValue="addFiles" />

                        <v-chip-group
                            v-if="documents"
                            class="mb-6 ml-10">
                            <v-chip
                                v-for="doc in documents"
                                :key="doc.id"
                                :closable="true"
                                @click:close="removeDocument(doc.id)">
                                {{ doc.name }}
                            </v-chip>
                        </v-chip-group>

                        <UsersSearch
                            :participants="meeting.participants"
                            :meeting="meeting"
                            @update:participants="meeting.participants = $event" />
                    </v-col>
                    <v-col cols="4">
                        <div
                            class="text-subtitle-1 text-grey-darken-1 text-button mb-4">
                            {{ $t('Advanced settings') }}
                            <v-divider />
                        </div>
                        <v-select
                            v-model="guestPolicy"
                            :label="$t('Guest access')"
                            :items="guestPolicyOptions"
                            prepend-icon="mdi-incognito"
                            :item-props="true" />
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
                            class="mr-4"
                            :disabled="loading"
                            prepend-icon="mdi-close"
                            @click="cancelCreateMeeting">
                            {{ $t('Cancel') }}
                        </v-btn>
                        <v-btn
                            :disabled="loading"
                            color="error"
                            prepend-icon="mdi-trash-can"
                            @click="deleteMeeting">
                            {{ $t('Delete') }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card>
        </v-form>
        <ConfirmationDialog
            ref="deleteDialog"
            color="error" />
    </v-col>
</template>

<script>
import { useToast } from 'vue-toastification'
import UsersSearch from '../chunks/UsersSearch.vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import moment from 'moment'
import ConfirmationDialog from '../chunks/ConfirmationDialog.vue'

const m = moment
const toast = useToast()

export default {
    name: 'CreateMeeting',
    components: {
        ConfirmationDialog,
        VueDatePicker,
        UsersSearch
    },
    data() {
        return {
            layout: null,
            guestPolicy: null,
            date: null,
            loading: false,
            caption: null,
            files: null,
            guestPolicyOptions: [
                {
                    title: this.$t('Allow'),
                    value: 'ALWAYS_ACCEPT'
                },
                {
                    title: this.$t('Deny'),
                    value: 'ALWAYS_DENY'
                },
                {
                    title: this.$t('Ask moderator'),
                    value: 'ASK_MODERATOR'
                }
            ],
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
        },
        documents() {
            return this.meeting.documents
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
                this.$store.commit('clearMeetingState')
                toast.error(this.$t('Error getting meeting'))
            })
        } else {
            this.$store.commit('clearMeetingState')
        }
        this.layout = this.meeting.meetingLayout
        this.guestPolicy = this.meeting.guestPolicy
        this.date = this.meeting.date
    },
    methods: {
        async saveMeeting(event) {
            this.loading = true
            this.meeting.guestPolicy = this.guestPolicy
            this.meeting.meetingLayout = this.layout
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
        },
        async removeDocument(id) {
            await this.$store.dispatch('removeDocument', id).then(() => {
                toast.success(this.$t('Document deleted'))
            })
        },
        async deleteMeeting() {
            if (this.id) {
                const ok = await this.$refs.deleteDialog.show({
                    okColor: 'white',
                    okIcon: 'mdi-trash-can',
                    title: this.$t('Delete this meeting?'),
                    message: this.$t('Sure delete meeting and participants?'),
                    okButton: this.$t('Delete')
                })
                if (ok) {
                    const res = await this.$store.dispatch('deleteMeeting', this.id)
                    if (res) {
                        this.$router.push({ name: 'meetings.my' })
                    }
                }
            }
        }
    }

}
</script>

<style scoped lang="scss">

</style>
