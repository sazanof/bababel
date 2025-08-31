<template>
    <VCol
        v-if="ready"
        class="fill-height">
        <VCard
            v-if="showMore"
            :title="caption">
            <VForm

                @submit.prevent="saveMeeting">
                <VRow

                    class="px-4">
                    <VCol
                        class="mr-4">
                        <div
                            class="text-subtitle-1 text-grey-darken-1 text-button mb-4">
                            {{ $t('Meeting parameters') }}
                            <VDivider />
                        </div>
                        <VTextField
                            v-model="meeting.name"
                            prepend-icon="mdi-text-shadow"
                            class="mb-6"
                            hide-details
                            :label="$t('Meeting name')" />
                        <VSelect
                            v-model="layout"
                            :label="$t('Style')"
                            :items="layoutOptions"
                            prepend-icon="mdi-palette-swatch-variant"
                            :item-props="true" />
                        <VueDatePicker
                            v-model="date"
                            :min-date="new Date()"
                            @update:model-value="meeting.date = formattedDate">
                            <template #trigger>
                                <VTextField
                                    :model-value="formattedDate"
                                    hide-details
                                    class="mb-6"
                                    :label="$t('Date')"
                                    prepend-icon="mdi-calendar" />
                            </template>
                        </VueDatePicker>
                        <VTextarea
                            v-model="meeting.welcome"
                            prepend-icon="mdi-text-long"
                            class="mb-6"
                            hide-details
                            :label="$t('Welcome message or description')" />

                        <VFileInput
                            hide-details
                            prepend-icon="mdi-paperclip"
                            :chips="true"
                            :multiple="true"
                            :label="$t('Files')"
                            @update:model-value="addFiles" />

                        <VChipGroup
                            v-if="documents"
                            class="mb-6 ml-10">
                            <VChip
                                v-for="doc in documents"
                                :key="doc.id"
                                :closable="true"
                                @click:close="removeDocument(doc.id)">
                                {{ doc.name }}
                            </VChip>
                        </VChipGroup>

                        <UsersSearch
                            :participants="meeting.participants"
                            :meeting="meeting"
                            @update:participants="meeting.participants = $event" />
                    </VCol>
                    <VCol
                        cols="4">
                        <div
                            class="text-subtitle-1 text-grey-darken-1 text-button mb-4">
                            {{ $t('Advanced settings') }}
                            <VDivider />
                        </div>
                        <VBtn
                            block
                            color="deep-orange"
                            :text="showMore ? $t('Simple mode') : $t('Extended mode')"
                            :prepend-icon="showMore ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                            class="mb-6"
                            @click="showMore = !showMore" />
                        <VSelect
                            v-model="guestPolicy"
                            :label="$t('Guest access')"
                            :items="guestPolicyOptions"
                            prepend-icon="mdi-incognito"
                            :item-props="true" />
                        <VSwitch
                            v-model="meeting.record"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Record')" />
                        <VSwitch
                            v-if="meeting.record"
                            v-model="meeting.autoStartRecording"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Auto start recording')" />
                        <VSwitch
                            v-model="meeting.webcamsOnlyForModerator"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Webcams only for moderator')" />
                        <VSwitch
                            v-model="meeting.allowModsToEjectCameras"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Managing participant cameras')" />
                        <VSwitch
                            v-model="meeting.muteOnStart"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Mute on start')" />
                        <VSwitch
                            v-model="meeting.lockSettingsDisableMic"
                            color="deep-orange"
                            :value="true"
                            :label="$t('All participants are listeners')" />
                        <VSwitch
                            v-model="meeting.allowModsToUnmuteUsers	"
                            color="deep-orange"
                            :value="true"
                            :label="$t('Managing participant microphones')" />
                    </VCol>
                </VRow>
                <VRow class="pb-4 px-4">
                    <VCol>
                        <div class="text-right">
                            <VBtn
                                class="mr-4"
                                :disabled="loading"
                                prepend-icon="mdi-close"
                                @click="cancelCreateMeeting">
                                {{ $t('Cancel') }}
                            </VBtn>
                            <VBtn
                                :loading="loading"
                                :disabled="loading"
                                type="submit"
                                class="mr-4"
                                prepend-icon="mdi-content-save"
                                color="deep-orange">
                                {{ $t('Save') }}
                            </VBtn>
                            <VBtn
                                v-if="id"
                                :disabled="loading"
                                color="error"
                                prepend-icon="mdi-trash-can"
                                @click="deleteMeeting">
                                {{ $t('Delete') }}
                            </VBtn>
                        </div>
                    </VCol>
                </VRow>
            </VForm>
        </VCard>
        <VCard
            v-else
            variant="text"
            class="bg fill-height d-flex align-center justify-center flex-wrap">
            <VSheet
                color="transparent"
                width="600"
                class="pa-6"
                style="z-index:100">
                <VTextField
                    v-model="meeting.name"
                    density="default"
                    variant="solo"
                    prepend-inner-icon="mdi-text-shadow"
                    hide-details
                    :label="$t('Meeting name')">
                    <template #append>
                        <VBtn
                            density="default"
                            size="x-large"
                            :loading="loading"
                            :disabled="loading"
                            type="submit"
                            class="px-6"
                            icon="mdi-send"
                            color="deep-orange"
                            @click="saveMeeting" />
                        <VBtn
                            density="default"
                            size="x-large"
                            class="ml-4"
                            variant="text"
                            :text="showMore ? $t('Simple mode') : $t('Extended mode')"
                            icon="mdi-dots-horizontal"
                            @click="showMore = !showMore" />
                    </template>
                </VTextField>
            </VSheet>
        </VCard>

        <ConfirmationDialog
            ref="deleteDialog"
            color="error" />
    </VCol>
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
            ready: false,
            layout: 'VIDEO_FOCUS',
            guestPolicy: 'ALWAYS_ACCEPT',
            date: null,
            loading: false,
            caption: null,
            files: null,
            showMore: false,
            guestPolicyOptions: [
                {
                    title: this.$t('Allow'),
                    value: 'ALWAYS_ACCEPT'
                },
                {
                    title: this.$t('Deny'),
                    value: 'ALWAYS_DENY'
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
            return this.date ? m(this.date).format('DD.MM.YYYY HH:mm') : this.meeting.date
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
        },
        editMode() {
            return this.id !== null && this.meeting !== null
        }
    },
    async created() {
        this.meeting.id = this.id
        if (this.meeting.id > 0) {
            await this.getMeeting().catch(e => {
                this.$store.commit('clearMeetingState')
                toast.error(this.$t('Error getting meeting'))
            })
            this.date = this.meeting.date
            this.showMore = true
        } else {
            this.$store.commit('clearMeetingState')
            this.date = m(new Date()).add(3, 'hours').toDate()
            this.meeting.date = this.formattedDate
        }
        this.layout = this.meeting.meetingLayout
        this.guestPolicy = this.meeting.guestPolicy
        this.ready = true
    },
    methods: {
        async saveMeeting(event) {
            this.loading = true
            if (this.meeting.welcome === null) {
                this.meeting.welcome = this.meeting.name
            }
            this.meeting.guestPolicy = this.guestPolicy
            this.meeting.meetingLayout = this.layout
            const data = this.meeting.toObject()
            if (this.id === null) {
                const res = await this.$store.dispatch('addMeeting', data)
                    .catch(e => {
                        toast.error(e.response.data.message)
                    })
                    .finally(() => {
                        this.loading = false
                    })
                if (res) {
                    this.$router.push({
                        name: 'meeting_page', params: {
                            id: res.id
                        }
                    })
                }
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
.bg {
    position: relative;

    &:before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        z-index: 11;
        background-image: url("../../img/meeting-bg.jpg");
        background-position: center;
        background-size: cover;
        overflow: hidden;
        opacity: 0.3;
    }
}

.actions {
    display: flex;
    justify-content: space-between;
    padding-left: 52px;
}
</style>
