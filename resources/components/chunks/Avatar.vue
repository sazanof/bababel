<template>
    <v-avatar
        class="avatar"
        :size="size">
        <v-img
            v-if="user.photo !== null"
            :src="user.photo" />
        <v-img
            v-else
            :src="`users/${user.id}/avatar/${size}`" />
    </v-avatar>
</template>

<script>

export default {
    name: 'Avatar',
    props: {
        user: {
            type: Object,
            required: true
        },
        size: {
            type: Number,
            default: 64
        }
    },
    computed: {
        fullName() {
            return `${this.user.firstname} ${this.user.lastname}`
        },
        initials() {
            return `${this.user.firstname[0]}${this.user.lastname[0]}`
        },
        color() {
            return `#${this.intToRGB(this.hashCode(this.user.email))}`
        }
    },
    methods: {
        hashCode(str) { // java String#hashCode
            let hash = 0
            for (let i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash)
            }
            return hash
        },

        intToRGB(i) {
            const c = (i & 0x00FFFFFF)
                .toString(16)
                .toUpperCase()

            return '00000'.substring(0, 6 - c.length) + c
        }
    }
}
</script>

<style lang="scss" scoped>
.avatar {

}

.initials {
    color: var(--color-white);
}
</style>
