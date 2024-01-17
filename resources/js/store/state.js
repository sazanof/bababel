import Meeting from '../../js/models/meeting.js'

export default {
    authenticated: false,
    user: null,
    settings: {
        rail: false,
        dateFormat: 'DD.MM.YYYY',
        timeFormat: 'HH:mm'
    },
    meeting: new Meeting(),
    meetings: [],
    activeJoinInfo: null
}
