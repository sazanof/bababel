import { createRouter, createWebHashHistory } from 'vue-router'
import Dashboard from '../components/routes/Dashboard.vue'
import CreateMeeting from '../components/routes/CreateMeeting.vue'
import MeetingsPage from '../components/routes/MeetingsPage.vue'
import BbbWindow from '../components/pages/BbbWindow.vue'
import MeetingPage from '../components/routes/MeetingPage.vue'
import MeetingLogoutPage from '../components/routes/MeetingLogoutPage.vue'
import Account from '../components/routes/Account.vue'

const routes = [
    {
        path: '/',
        component: Dashboard
    },
    {
        path: '/account',
        component: Account,
        name: 'account'
    },
    {
        path: '/bbb',
        name: 'bbb',
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
                component: MeetingsPage,
                name: 'meetings.records',
                props: {
                    criteria: 'records'
                }
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
            },
            {
                path: ':id(\\d+)/view',
                name: 'meeting_page',
                component: MeetingPage
            },
            {
                path: ':id(\\d+)/logout',
                name: 'meeting_logout_page',
                component: MeetingLogoutPage
            }
        ]
    }


]

const router = createRouter({
    history: createWebHashHistory(),
    routes // short for `routes: routes`
})

export default router
