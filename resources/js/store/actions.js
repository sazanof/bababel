import axios from 'axios'

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
        return await axios.put(`${PANEL}meetings/${data.id}`, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    }
}
