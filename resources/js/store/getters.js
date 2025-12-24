export default {
    isAuthenticated(state) {
        return state.authenticated
    },
    getUser(state) {
        return state.user
    },
    getNotifications(state) {
        return state.notifications
    },
    getSettings(state) {
        return state.settings
    },
    getMeeting(state) {
        return state.meeting
    },
    getMeetings(state) {
        return state.meetings
    },
    getJoinInfo(state) {
        return state.activeJoinInfo
    },
    getRail(state) {
        return state.settings.rail
    },
    getBbbWindowLeft(state) {
        return state.settings.bbbWindowLeft
    }
}
