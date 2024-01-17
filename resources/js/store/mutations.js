export default {
    logIn(state, data) {
        state.authenticated = true
        state.user = data
    },
    logOut(state) {
        state.authenticated = false
        state.user = null
    },
    setMeeting(state, data) {
        if (state.meetings.hasOwnProperty('data')) {
            state.meetings.data.find(meeting => {
                if (meeting.id === data.id) {
                    Object.assign(meeting, data)
                }
            })
        }

        state.meeting.update(data)
    },
    deleteMeeting(state, id) {
        if (state.meetings.hasOwnProperty('data')) {
            state.meetings = state.meetings.data.filter(meeting => {
                return meeting.id !== id
            })
        }
        state.meeting.reset()
    },
    setMeetings(state, meetings) {
        state.meetings = meetings
    },
    clearMeetingState(state) {
        state.meeting.reset()
    },
    setActiveJoinInfo(state, join) {
        state.activeJoinInfo = join
    },
    resetActiveJoinInfo(state) {
        state.activeJoinInfo = null
    },
    setUser(state, user) {
        state.user = user
    },
    updateAvatar(state, blobUrl) {
        state.user.photo = blobUrl
    },
    setRail(state, rail) {
        state.settings.rail = rail
    },
    setBbbWindowLeft(state, left) {
        state.settings.bbbWindowLeft = left
    }
}
