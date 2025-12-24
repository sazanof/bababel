import Meeting from '../../js/models/meeting.js'

export default {
    authenticated: false,
    notifications: [],
    user: null,
    settings: {
        rail: false,
        dateFormat: 'DD.MM.YYYY',
        timeFormat: 'HH:mm',
        bbbWindowLeft: 0
    },
    meeting: new Meeting(),
    meetings: [],
    activeJoinInfo: null
}
