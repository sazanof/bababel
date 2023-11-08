import axios from 'axios'
import meetings from '../../components/chunks/Meetings.vue'

const AUTH = '/auth/'
const PANEL = '/panel/'
const ADMIN = '/admin/'
const BBB = '/bbb/'

axios.interceptors.response.use(function (response) {
    // Optional: Do something with response data
    return response
}, function (error) {
    if (error.response.status === 419 || error.response.status === 401) {
        window.location.reload()
    }
    return Promise.reject(error)
})

export default {
    async checkAuth({ commit }) {
        await axios.get(`${AUTH}check`).then(res => {
            commit('logIn', res.data)
        })
    },
    async logIn({ commit }, data) {
        await axios.post(`${AUTH}login`, data).then(res => {
            commit('logIn', res.data)
        })
    },
    async logOut({ commit }) {
        await axios.get(`${AUTH}logout`).then(() => {
            commit('logOut')
        })
    },
    async searchUsers({ _ }, term) {
        return await axios.get(`${PANEL}search/users?term=${term}`).then(res => {
            return res.data
        })
    },

    async addMeeting({ _ }, data) {
        return await axios.post(`${PANEL}meetings`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },
    async editMeeting({ _ }, data) {
        return await axios.post(`${PANEL}meetings/${data.id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },
    async getMeetings({ commit }, filter) {
        return await axios.post(`${PANEL}meetings/${filter.criteria}`, filter).then(res => {
            commit('setMeetings', res.data)
            return res.data
        })
    },
    async getMeeting({ commit }, id) {
        return await axios.get(`${PANEL}meetings/${id}`).then(res => {
            return commit('setMeeting', res.data)
        })
    },
    async startMeeting({ commit }, id) {
        return await axios.get(`${PANEL}meetings/${id}/start`).then(res => {
            if (res.data.meeting) {
                commit('setMeeting', res.data.meeting)
            }
        })
    },
    async stopMeeting({ commit }, id) {
        return await axios.get(`${PANEL}meetings/${id}/stop`).then(res => {
            if (res.data.success) {
                if (res.data.meeting) {
                    commit('setMeeting', res.data.meeting)
                }
            }
        })
    },
    async joinMeeting({ commit }, data) {
        return await axios.post(`${PANEL}meetings/${data.id}/join`, data).then(res => {
            return res.data
            //return commit('setMeeting', res.data)
        })
    },
    async getMeetingInfo({ commit }, id) {
        return await axios.get(`${PANEL}meetings/${id}/info`).then(res => {
            return commit('setMeeting', res.data)
        })
    },
    async deleteMeeting({ commit }, id) {
        return await axios.get(`${PANEL}meetings/${id}/delete`).then(res => {
            //return commit('setMeeting', res.data)
        })
    }
}
