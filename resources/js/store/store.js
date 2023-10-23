import { createStore } from 'vuex'
import actions from './actions.js'
import state from './state.js'
import mutations from './mutations.js'
import getters from './getters.js'
const store = createStore({
    actions,
    mutations,
    getters,
    state
})
export default store
