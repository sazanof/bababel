<template>
    <v-navigation-drawer
        :rail="opened"
        :temporary="$route.name === 'bbb'"
        @update:rail="onUpdateRail">
        <v-sheet
            color="deep-orange">
            <div
                class="user-block"
                :class="{mini : rail}">
                <Avatar
                    class="avatar"
                    :size="!rail ? 64 : 42"
                    :user="user" />
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
        </v-sheet>

        <v-divider color="blue-grey-darken-4" />

        <v-list class="pa-0">
            <v-list-item
                v-for="[icon, text, path] in links"
                :key="icon"
                :value="text"
                :prepend-icon="icon"
                :title="text"
                :active="$route.path === path"
                color="blue-grey-darken-1"
                link
                @click="$router.push(path)" />
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import Avatar from './Avatar.vue'

export default {
    name: 'Sidebar',
    components: { Avatar },
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
    emits: [ 'on-update-rail' ],
    data() {
        return {
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
        }
    }
}
</script>

<style lang="scss" scoped>
.user-block {
    min-height: 64px;
    transition: 0.5s;

    .name {
        font-weight: bold;
    }

    padding: 6px;

    &.mini {
        padding-top: 10px;
    }

    &:not(.mini) {
        .avatar {
            margin-bottom: 10px;
        }
    }
}

</style>
