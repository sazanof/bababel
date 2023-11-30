<template>
    <div class="dialogs">
        <v-dialog
            v-model="show"
            height="90%"
            :scrollable="true"
            transition="dialog-bottom-transition">
            <template #default="{ isActive }">
                <v-card>
                    <v-toolbar color="deep-orange">
                        <v-toolbar-title>{{ meeting.name }}</v-toolbar-title>
                        <v-spacer />
                        <v-btn
                            v-if="participants.length > 0 && tab === 'participants'"
                            icon="mdi-magnify" />
                        <v-btn
                            icon="mdi-close"
                            @click="isActive.value = false" />

                        <template #extension>
                            <v-tabs
                                v-model="tab"
                                :show-arrows="true"
                                :center-active="true"
                                align-tabs="title">
                                <v-tab
                                    value="info"
                                    prepend-icon="mdi-text">
                                    {{ $t('Meeting details') }}
                                </v-tab>
                                <v-tab
                                    value="participants"
                                    prepend-icon="mdi-account-multiple">
                                    {{ $t('Participants') }}
                                </v-tab>
                                <v-tab
                                    v-if="hasRecords"
                                    value="records"
                                    prepend-icon="mdi-record">
                                    {{ $t('Recordings') }}
                                </v-tab>
                            </v-tabs>
                        </template>
                    </v-toolbar>
                    <v-card-text class="pa-10">
                        <v-window v-model="tab">
                            <v-window-item
                                value="info">
                                {{ meeting.welcome }}
                            </v-window-item>

                            <v-window-item value="participants">
                                <ParticipantsList
                                    :meeting="meeting"
                                    :participants="participants" />
                            </v-window-item>

                            <v-window-item
                                v-if="hasRecords"
                                value="records">
                                <v-list>
                                    <RecordItem
                                        v-for="record in records"
                                        :key="record.id"
                                        :meeting="meeting"
                                        :record="record" />
                                </v-list>
                            </v-window-item>
                        </v-window>
                    </v-card-text>
                </v-card>
                <v-footer>
                    <v-row
                        justify="end"
                        class="pa-4"
                        :no-gutters="true">
                        <v-btn
                            v-if="canStart"
                            class="ml-4"
                            :loading="startLoading"
                            :disabled="loading"
                            prepend-icon="mdi-play"
                            color="deep-orange"
                            variant="flat"
                            :text="$t('Start')"
                            @click="startMeeting" />
                        <v-btn
                            v-else-if="meeting.status === 1 || meeting.status === 2"
                            class="ml-4"
                            :disabled="loading"
                            prepend-icon="mdi-account-voice"
                            color="deep-orange"
                            variant="flat"
                            :text="$t('Join')"
                            @click="preJoinMeeting" />
                        <v-btn
                            v-if="meeting.status === 2 && isOwner"
                            class="ml-4"
                            :disabled="loading"
                            prepend-icon="mdi-stop"
                            color="primary"
                            variant="flat"
                            :text="$t('Stop')"
                            @click="stopMeeting" />
                        <v-btn
                            v-if="isOwner"
                            class="ml-4"
                            :disabled="loading"
                            prepend-icon="mdi-pencil"
                            color="info"
                            variant="flat"
                            :text="$t('Edit')"
                            @click="$router.push({name:'edit_meeting',params:{id:meeting.id}})" />
                    </v-row>
                </v-footer>
            </template>
        </v-dialog>
        <v-dialog
            v-model="showJoin"
            width="400"
            @close="close">
            <template #default="{isActive}">
                <v-card :title="$t('Join to meeting')">
                    <v-card-text>
                        <v-text-field
                            v-model="visibleName"
                            :label="$t('Enter visible name')" />
                        <v-btn
                            v-if="link && duplicate"
                            color="deep-orange"
                            prepend-icon="mdi-link"
                            width="100%"
                            :href="link"
                            target="_blank">
                            {{ $t('Previous link') }}
                        </v-btn>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer />
                        <v-btn
                            color="deep-orange"
                            variant="flat"
                            :loading="joinLoader"
                            :disabled="joinLoader"
                            prepend-icon="mdi-check"
                            :text="$t('Join')"
                            @click="joinMeeting" />
                        <v-btn
                            prepend-icon="mdi-close"
                            :text="$t('Cancel')"
                            @click="isActive.value = false" />
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script>
import RecordItem from './RecordItem.vue'
import { useToast } from 'vue-toastification'
import ParticipantsList from './ParticipantsList.vue'

const toast = useToast()
export default {
    name: 'MeetingDialogs',
    components: {
        ParticipantsList,
        RecordItem
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        showMore: {
            type: Boolean,
            default: false
        },
        meeting: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-info-change', 'on-join-success' ],
    data() {
        return {
            tab: null,
            duplicate: false,
            joinLoader: false,
            visibleName: null,
            link: null,
            show: false,
            showJoin: false,
            loading: false,
            startLoading: false,
            joinLoading: false
        }
    },
    computed: {
        participants() {
            return this.meeting.participants
        },
        records() {
            return this.meeting?.records
        },
        currentParticipant() {
            return this.meeting.participants.find(p => {
                return p.id === this.user.id
            })
        },
        isOwner() {
            return this.user.id === this.meeting.userId
        },
        canStart() {
            return this.isOwner && (this.meeting.status === 0 || this.meeting.status === 3)
        },
        hasRecords() {
            return this.meeting?.records && this.meeting?.records.length > 0
        }
    },
    watch: {
        showMore() {
            this.show = this.showMore
            this.showJoin = this.showPreJoin
        },
        show() {
            this.$emit('on-info-change', this.show)
        }
    },
    created() {
        this.visibleName = `${this.user.lastname} ${this.user.firstname}`
    },
    methods: {
        preJoinMeeting() {
            this.showJoin = true
        },
        async startMeeting() {
            if (!this.canStart) {
                return false
            }
            this.startLoading = true
            this.loading = true
            await this.$store.dispatch('startMeeting', this.meeting.id)
                .then(() => {
                    this.joinMeeting()
                })
                .catch(e => {
                    alert(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                    this.startLoading = false
                })
        },
        async joinMeeting() {
            this.duplicate = false
            this.link = null
            this.loading = true
            this.joinLoader = true
            const joinInfo = await this.$store.dispatch('joinMeeting', {
                id: this.meeting.id,
                visibleName: this.visibleName
            })
                .catch(e => {
                    this.duplicate = true
                    this.link = e.response.data.link
                    toast.error(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                    this.joinLoader = false
                })
            if (joinInfo) {
                this.close()
                const url = joinInfo.join.url
                this.$emit('on-join-success', {
                    url,
                    duplicate: this.duplicate
                })
                window.open(url, '_blank').focus()
            }
        },
        async stopMeeting() {
            this.loading = true
            await this.$store.dispatch('stopMeeting', this.meeting.id)
                .then(() => {
                    this.joinLoader = true
                })
                .catch(e => {
                    alert(e.response.data.message)
                })
                .finally(() => {
                    this.loading = false
                })
        },
        open() {
            this.show = true
        },
        join() {
            this.showJoin = true
        },
        close() {
            this.show = false
            this.showJoin = false
        }
    }
}
</script>

<style scoped>

</style>
