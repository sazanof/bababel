<template>
    <v-app
        v-if="user !== undefined"
        id="inspire"
        class="bg-blue-grey-lighten-5"
        :full-height="true">
        <Sidebar
            :user="user"
            :drawer="drawer"
            @on-update-drawer="drawer = !drawer" />
        <AppHeader @on-menu-click="drawer = !drawer" />
        <v-main>
            <v-container
                fluid>
                <v-row>
                    <router-view />
                </v-row>
            </v-container>
        </v-main>
        <div class="create-menu">
            <v-menu>
                <template #activator="{ props }">
                    <v-btn
                        icon="mdi-plus"
                        color="deep-orange"
                        v-bind="props"
                        size="x-large" />
                </template>
                <v-list>
                    <v-list-item
                        :value="1"
                        prepend-icon="mdi-video-account"
                        @click="$router.push({name: 'create_meeting'})">
                        <v-list-item-title>{{ $t('Create meeting') }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </div>
    </v-app>
</template>

<script>
import Sidebar from '../chunks/Sidebar.vue'
import AppHeader from '../chunks/AppHeader.vue'

export default {
    name: 'Index',
    components: {
        AppHeader,
        Sidebar
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            drawer: true
        }
    }

}
</script>
<style lang="scss" scoped>
.create-menu {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 10;
}
</style>
