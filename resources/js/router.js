import { createRouter, createWebHashHistory } from 'vue-router'
import Dashboard from '../components/routes/Dashboard.vue'
import CreateMeeting from '../components/routes/CreateMeeting.vue'
import MeetingsPage from '../components/routes/MeetingsPage.vue'
import BbbWindow from '../components/pages/BbbWindow.vue'

const routes = [
    {
        path: '/',
        component: Dashboard
    },

    {
        path: '/bbb',
        name: 'BBB',
        component: BbbWindow
    },

    {
        path: '/meetings',
        children: [
            {
                path: '',
                component: MeetingsPage,
                name: 'meetings.my',
                props: {
                    criteria: 'my'
                }
            },
            {
                path: 'invitations',
                component: MeetingsPage,
                name: 'meetings.invitations',
                props: {
                    criteria: 'invitations'
                }
            },
            {
                path: 'past',
                component: MeetingsPage,
                name: 'meetings.past',
                props: {
                    criteria: 'past'
                }
            },
            {
                path: 'records',
                component: Dashboard
            },
            {
                path: 'create',
                name: 'create_meeting',
                component: CreateMeeting
            },
            {
                path: ':id(\\d+)',
                name: 'edit_meeting',
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