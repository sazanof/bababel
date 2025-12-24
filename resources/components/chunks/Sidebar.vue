<template>
    <VNavigationDrawer
        v-model="drawer"
        rounded="0"
        :rail="opened"
        :permanent="true"
        @update:rail="onUpdateRail($event)">
        <VSheet
            rounded="0"
            class="header-bg"
            color="deep-orange">
            <div
                class="user-block"
                :class="{mini : rail}">
                <Avatar
                    class="avatar"
                    :size="!rail ? 64 : 42"
                    :user="user"/>
                <div
                    v-if="!rail"
                    class="name">
                    {{ fullName }}
                </div>
                <div
                    v-if="!rail">
                    {{ user.email }}
                </div>
            </div>
        </VSheet>

        <VList
            rounded="0"
            class="pa-0">
            <VListItem
                rounded="0"
                variant="flat"
                base-color="deep-orange"
                prepend-icon="mdi-plus-circle"
                :title="$t('Create meeting')"
                @click="openCreateMeeting"/>
            <VListItem
                v-for="[icon, text, path] in links"
                :key="icon"
                rounded="0"
                class="py-4"
                :value="text"
                :prepend-icon="icon"
                :title="text"
                :active="$route.path === path"
                color="blue-grey-darken-1"
                link
                @click="$router.push(path)"/>
        </VList>
    </VNavigationDrawer>
</template>

<script>
import Avatar from './Avatar.vue'

export default {
    name: 'Sidebar',
    components: {Avatar},
    props: {
        rail: {
            type: Boolean,
            default: true
        },
        user: {
            type: Object,
            required: true
        }
    },
    emits: ['on-update-rail'],
    data() {
        return {
            drawer: true,
            opened: false,
            links: [
                [
                    'mdi-view-dashboard',
                    this.$t('Dashboard'),
                    '/'
                ],
                [
                    'mdi-crown',
                    this.$t('My meetings'),
                    '/meetings/'
                ],
                [
                    'mdi-heart',
                    this.$t('Invitations'),
                    '/meetings/invitations'
                ],
                [
                    'mdi-archive',
                    this.$t('Past meetings'),
                    '/meetings/past'
                ],
                [
                    'mdi-record-rec',
                    this.$t('Meeting records'),
                    '/meetings/records'
                ]
            ]
        }
    },
    computed: {
        fullName() {
            return `${this.user.firstname} ${this.user.lastname}`
        }
    },
    watch: {
        rail() {
            this.opened = this.rail
        }
    },
    created() {
        this.opened = this.$route.path !== '/bbb'
    },
    methods: {
        onUpdateRail() {
            this.$emit('on-update-rail', this.rail)
        },
        openCreateMeeting() {
            this.$store.commit('clearMeetingState')
            this.$router.push({name: 'create_meeting'})
        }
    }
}
</script>

<style lang="scss" scoped>
.header-bg {
    position: relative;
    overflow: hidden;

    &:after {
        content: "";
        position: absolute;
        top: -10px;
        right: -10px;
        left: -10px;
        bottom: -10px;
        z-index: 11;
        background-image: url("../../img/meeting-bg.jpg");
        background-position: center;
        background-size: cover;
        overflow: hidden;
        filter: blur(5px);
        opacity: 0.7;
    }

    &:before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        z-index: 12;
        background: var(--color-orange);
        opacity: 0.5;
    }
}

.user-block {
    z-index: 100;
    position: relative;
    min-height: 64px;
    transition: 0.5s;

    .name {
        font-weight: bold;
    }

    padding: 16px;

    &.mini {
        padding: 10px 6px 6px 6px;
    }

    &:not(.mini) {
        .avatar {
            margin-bottom: 10px;
        }
    }
}

</style>
