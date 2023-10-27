<template>
    <div class="user-search">
        <v-menu
            v-model="menu"
            :open-on-click="true"
            :close-on-content-click="false"
            :max-height="300">
            <template #activator="{ props, on }">
                <v-text-field
                    v-bind="props"
                    v-model="query"
                    :hide-details="true"
                    prepend-icon="mdi-account-group"
                    :loading="loading"
                    v-on="on"
                    @click="menu = true"
                    @close="menu = false"
                    @keyup="debounceOnSearchUsers" />
            </template>
            <v-list
                v-if="users.length > 0"
                class="ms-10 py-6">
                <v-list-item
                    v-for="(user, i) in users"
                    :key="user.id"
                    :value="user.id"
                    @click="onClick(user, i)">
                    <template #title>
                        <div class="fullname">
                            {{ user.lastname }} {{ user.firstname }}
                        </div>
                    </template>
                    <template #subtitle>
                        <div class="position">
                            {{ user.department }}, {{ user.position }}
                        </div>
                    </template>
                    <template #prepend>
                        <Avatar
                            :user="user"
                            :size="32" />
                    </template>
                </v-list-item>
            </v-list>
            <v-list
                v-else
                class="ms-10">
                <v-list-item>{{ $t('Empty') }}</v-list-item>
            </v-list>
        </v-menu>

        <v-list
            v-if="selectedUsers.length > 0"
            class="ps-6 py-6">
            <v-list-item
                v-for="user in selectedUsers"
                :key="user.id"
                class="mb-2">
                <template #title>
                    <div class="fullname">
                        {{ user.lastname }} {{ user.firstname }}
                    </div>
                </template>
                <template #subtitle>
                    <div class="position">
                        {{ user.department }}, {{ user.position }}
                    </div>
                </template>
                <template #prepend>
                    <Avatar
                        :user="user"
                        :size="48" />
                </template>
                <template #append>
                    <v-btn
                        v-model="user.isModerator"
                        :class="user.isModerator ? 'text-yellow-accent-4' : 'text-blue-grey-lighten-4'"
                        :icon="user.isModerator ? 'mdi-crown' : 'mdi-crown-outline' "
                        variant="plain"
                        @click="user.isModerator = !user.isModerator" />

                    <v-btn
                        color="red"
                        icon="mdi-close"
                        variant="plain"
                        @click="deleteUser(user)" />
                </template>
            </v-list-item>
        </v-list>
    </div>
</template>

<script>
import Avatar from './Avatar.vue'
import debounce from '../../js/helpers/debounce.js'

export default {
    name: 'UsersSearch',
    components: {
        Avatar
    },
    data() {
        return {
            loading: false,
            query: '',
            deb: debounce(this.onSearchUsers, 300),
            users: [],
            selectedUsers: [],
            selected: null,
            menu: false
        }
    },
    computed: {
        selectedUsersIDs() {
            return this.selectedUsers.map(u => u.id)
        }
    },
    methods: {
        async onSearchUsers() {
            this.users = await this.$store.dispatch('searchUsers', this.query).finally(() => {
                this.loading = false
            })
            this.users = this.users.filter(user => this.selectedUsersIDs.indexOf(user.id) === -1)
        },
        debounceOnSearchUsers() {
            this.loading = true
            this.deb()
        },
        onClick(u, i) {
            this.users = this.users.filter((user, index) => {
                return index !== i
            })
            if (!this.selectedUsers.find(user => u.id === user.id)) {
                this.selectedUsers.push(u)
            }
        },
        deleteUser(user) {
            this.selectedUsers = this.selectedUsers.filter(u => u.id !== user.id)
        }
    }
}
</script>

<style lang="scss" scoped>
.custom-chip {
    padding-left: 0;
}
</style>
