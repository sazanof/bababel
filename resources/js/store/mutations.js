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
        state.meetings.data.find(meeting => {
            if (meeting.id === data.id) {
                Object.assign(meeting, data)
            }
        })
        state.meeting.update(data)
    },
    setMeetings(state, meetings) {
        state.meetings = meetings
    },
    clearMeetingState(state) {
        state.meeting.reset()
    }
}
