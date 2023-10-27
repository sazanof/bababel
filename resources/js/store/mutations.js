export default {
    logIn(state, data) {
        state.authenticated = true
        state.user = data
    },
    logOut(state) {
        state.authenticated = false
        state.user = null
    }
}
