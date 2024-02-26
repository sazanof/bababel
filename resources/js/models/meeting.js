import moment from 'moment'

export default class Meeting {
    constructor() {
        this.id = null
        this.name = null
        this.welcome = null
        this.record = true
        this.autoStartRecording = false
        this.webcamsOnlyForModerator = false
        this.muteOnStart = false
        this.lockSettingsDisableMic = false
        this.allowModsToUnmuteUsers = true
        this.allowModsToEjectCameras = true
        this.participants = []
        this.files = null
        this.meetingLayout = 'VIDEO_FOCUS'
        this.guestPolicy = 'ALWAYS_DENY'
        this.resetDate()
    }

    reset() {
        this.id = null
        this.name = null
        this.welcome = null
        this.record = true
        this.autoStartRecording = false
        this.webcamsOnlyForModerator = false
        this.muteOnStart = false
        this.lockSettingsDisableMic = false
        this.allowModsToUnmuteUsers = true
        this.allowModsToEjectCameras = true
        this.participants = []
        this.files = null
        this.meetingLayout = 'VIDEO_FOCUS'
        this.guestPolicy = 'ALWAYS_DENY'
        this.resetDate()
    }

    update(data) {
        Object.assign(this, data)
    }

    resetDate() {
        this.date = moment(new Date()).format('DD.MM.YYYY HH:mm')
    }

    toObject() {
        return {
            id: this.id,
            date: this.date,
            name: this.name,
            welcome: this.welcome,
            record: this.record,
            autoStartRecording: this.autoStartRecording,
            webcamsOnlyForModerator: this.webcamsOnlyForModerator,
            muteOnStart: this.muteOnStart,
            lockSettingsDisableMic: this.lockSettingsDisableMic,
            allowModsToUnmuteUsers: this.allowModsToUnmuteUsers,
            allowModsToEjectCameras: this.allowModsToEjectCameras,
            participants: this.participants.map(p => {
                return {
                    id: p.id,
                    isModerator: p.isModerator ?? false
                }
            }),
            files: this.files,
            meetingLayout: this.meetingLayout,
            guestPolicy: this.guestPolicy
        }
    }
}
