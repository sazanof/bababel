<template>
    <v-col
        cols="4">
        <div
            class="participant-item"
            :class="{'bg-blue-grey-lighten-5': isModerator && !isOrganizer,'bg-yellow-lighten-5': isOrganizer, 'pa-2 rounded-xl': true}">
            <div class="avatar">
                <Avatar
                    :user="user"
                    :size="48" />
            </div>
            <div class="info">
                <div class="name">
                    {{ fullName }}
                </div>
                <div class="who">
                    {{ user.position }}, {{ user.department }}
                </div>
                <div class="who">
                    {{ user.email }}
                </div>
                <div class="who">
                    {{ user.phone }}
                </div>
                <div class="roles text-overline">
                    <span v-if="isOrganizer">
                        {{ $t('Organizer') }}
                    </span>
                    <span v-if="isModerator">
                        {{ $t('Moderator') }}
                    </span>
                    <span v-if="!isOrganizer && !isModerator">
                        {{ $t('Participant') }}
                    </span>
                </div>
            </div>
        </div>
    </v-col>
</template>

<script>
import Avatar from './Avatar.vue'

export default {
    name: 'ParticipantItem',
    components: {
        Avatar
    },

    props: {
        user: {
            type: Object,
            required: true
        },
        meeting: {
            type: Object,
            required: true
        }
    },
    computed: {
        fullName() {
            return `${this.user.lastname} ${this.user.firstname}`
        },
        isModerator() {
            return this.user.isModerator
        },
        isOrganizer() {
            return this.user.isOrganizer
        }
    }
}
</script>

<style scoped lang="scss">
.participant-item {
    display: flex;

    .info {
        margin-left: 8px;
        font-size: 14px;

        .name {
            font-weight: bold;
        }
    }
}

.roles {
    display: flex;

    span {
        display: inline-block;
        margin-right: 6px;
    }
}
</style>
