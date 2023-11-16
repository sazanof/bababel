<template>
    <div class="dialogs">
        <v-dialog
            v-model="show"
            transition="dialog-bottom-transition">
            <template #default="{ isActive }">
                <v-card>
                    <v-card-title>{{ meeting.name }}</v-card-title>
                    <v-card-text class="pa-4">
                        {{ meeting.welcome }}
                        <ParticipantsList
                            :meeting="meeting"
                            :participants="participants" />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer />
                        <div class="pa-4 d-flex justify-space-between">
                            <div>
                                <v-btn
                                    v-if="canStart"
                                    :loading="startLoading"
                                    :disabled="loading"
                                    prepend-icon="mdi-play"
                                    color="deep-orange"
                                    variant="flat"
                                    :text="$t('Start')"
                                    @click="startMeeting" />
                                <v-btn
                                    v-else-if="meeting.status === 1 || meeting.status === 2"
                                    :disabled="loading"
                                    prepend-icon="mdi-account-voice"
                                    color="deep-orange"
                                    variant="flat"
                                    :text="$t('Join')"
                                    @click="preJoinMeeting" />
                                <v-btn
                                    v-if="meeting.status === 2 && isOwner"
                                    :disabled="loading"
                                    prepend-icon="mdi-stop"
                                    color="primary"
                                    variant="flat"
                                    :text="$t('Stop')"
                                    @click="stopMeeting" />
                                <v-btn
                                    v-if="isOwner"
                                    :disabled="loading"
                                    prepend-icon="mdi-pencil"
                                    color="info"
                                    variant="flat"
                                    :text="$t('Edit')"
                                    @click="$router.push({name:'edit_meeting',params:{id:meeting.id}})" />
                            </div>
                            <div>
                                <v-btn
                                    :disabled="loading"
                                    class="ml-2"
                                    prepend-icon="mdi-close"
                                    :text="$t('Close')"
                                    @click="isActive.value = false" />
                            </div>
                        </div>
                    </v-card-actions>
                </v-card>
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
import { useToast } from 'vue-toastification'
import ParticipantsList from './ParticipantsList.vue'

const toast = useToast()
export default {
    name: 'MeetingDialogs',
    components: {
        ParticipantsList
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
