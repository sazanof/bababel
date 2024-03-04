import moment from 'moment'

const defaultFormat = 'DD.MM.YYYY HH:mm'

export function formatDate(date, format) {
    if (!format) {
        format = defaultFormat
    }
    return moment(date).format(format)
}
