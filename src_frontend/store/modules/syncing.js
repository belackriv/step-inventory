// initial state
const state = {
  isSyncing: false,
  requestCount: null
};

// mutations
const mutations = {
  inc (state, componentName) {
    state.requestCount++;
    state.isSyncing = (state.requestCount > 0);
  },
  dec (state) {
    state.requestCount--;
    state.isSyncing = (state.requestCount > 0);
  }
};

export default {
  namespaced: true,
  state,
  mutations
};
