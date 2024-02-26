<template>
    <v-list-item v-if="user !== null">
        <template #prepend>
            <Avatar
                :user="user"
                :size="64" />
        </template>
        <template #title>
            {{ fullName }}
        </template>
        <template #subtitle>
            {{ user.department }}, {{ user.position }}<br>
            {{ user.phone }}<br>
            {{ user.email }}
        </template>
    </v-list-item>
</template>

<script>
import Avatar from './Avatar.vue'

export default {
    name: 'MeetingOwner',
    components: {
        Avatar
    },
    props: {
        user: {
            type: Object,
            default: null
        }
    },
    computed: {
        fullName() {
            return `${this.user.lastname} ${this.user.firstname}`
        }
    }
}
</script>

<style scoped>

</style>
