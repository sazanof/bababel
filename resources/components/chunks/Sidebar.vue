<template>
    <v-navigation-drawer
        v-model="opened"
        :temporary="$route.path === '/bbb'"
        @update:modelValue="$emit('on-update-drawer', $event)">
        <v-sheet
            color="deep-orange"
            class="pa-4">
            <Avatar
                class="mb-4"
                :size="opened ? 64 : 40"
                :user="user" />
            <div
                class="name">
                {{ fullName }}
            </div>
            <div>
                {{ user.email }}
            </div>
        </v-sheet>

        <v-divider color="blue-grey-darken-4" />

        <v-list>
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
        drawer: {
            type: Boolean,
            default: true
        },
        user: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-update-drawer' ],
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
        drawer() {
            this.opened = this.drawer
        }
    },
    created() {
        this.opened = this.$route.path !== '/bbb'
    }
}
</script>

<style lang="scss" scoped>
.name {
    font-weight: bold;
}
</style>
