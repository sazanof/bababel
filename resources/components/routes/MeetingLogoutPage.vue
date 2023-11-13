<template>
    <div class="meeting-page">
        <v-sheet
            v-if="meeting"
            elevation="12"
            max-width="800"
            rounded="lg"
            width="100%"
            class="pa-4 text-center mx-auto meeting-view">
            <h2 class="text-h5 mb-6">
                {{ meeting.meeting.name }}
            </h2>

            <p class="mb-4 text-medium-emphasis text-body-2">
                {{ $t('You leaved meeting') }}
            </p>

            <v-btn
                v-if="user !== null"
                class="mr-4"
                prepend-icon="mdi-home"
                color="deep-orange"
                @click="$router.push('/')">
                {{ $t('To dashboard') }}
            </v-btn>

            <v-btn
                prepend-icon="mdi-link"
                color="deep-orange"
                @click="$router.push({name: 'meeting_page', params:{
                    id: id}
                })">
                {{ $t('Back to meeting page') }}
            </v-btn>
        </v-sheet>
    </div>
</template>

<script>
export default {
    name: 'MeetingLogoutPage',
    data() {
        return {
            meeting: null
        }
    },
    computed: {
        id() {
            return this.$route.params.id
        },
        user() {
            return this.$store.getters['getUser']
        }
    },
    async created() {
        await this.viewMeeting()
    },
    methods: {
        async viewMeeting() {
            if (this.id > 0) {
                this.meeting = await this.$store.dispatch('viewMeeting', this.id)
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.meeting-page {
    background: rgba(0, 0, 0, 0.4);
    position: fixed;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;

    &:before {
        content: "";
        position: fixed;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        z-index: 2;
        background: url("/resources/img/meeting-bg.jpg") center center;
        background-size: cover;
        opacity: 0.4;
        filter: blur(15px);
    }

    .meeting-view {
        max-width: 800px;
        width: 90%;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        z-index: 10;

    }
}
</style>
