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
    deleteRecord(record) {

    }
}
