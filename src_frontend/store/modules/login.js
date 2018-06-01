// initial state
const state = {
  username: null,
  password: null
};

// mutations
const mutations = {
  updateUsername (state, username) {
    state.username = username;
  },
  updatePassword (state, password) {
    state.password = password;
  }
};

export default {
  namespaced: true,
  state,
  mutations
};
