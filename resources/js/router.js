import { createRouter, createWebHashHistory } from 'vue-router'
import Dashboard from '../components/routes/Dashboard.vue'
import CreateMeeting from '../components/routes/CreateMeeting.vue'

const routes = [
    {
        path: '/',
        component: Dashboard
    },

    {
        path: '/meetings',
        children: [
            {
                path: '',
                component: Dashboard
            },
            {
                path: 'invitations',
                component: Dashboard
            },
            {
                path: 'past',
                component: Dashboard
            },
            {
                path: 'records',
                component: Dashboard
            },
            {
                path: 'create',
                name: 'create_meeting',
                component: CreateMeeting
            }
        ]
    }


]

const router = createRouter({
    history: createWebHashHistory(),
    routes // short for `routes: routes`
})

export default router
