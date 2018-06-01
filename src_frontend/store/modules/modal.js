// initial state
const state = {
  visible: false,
  componentName: null
};

// mutations
const mutations = {
  show (state, componentName) {
    state.visible = true;
    state.componentName = componentName;
  },
  hide (state) {
    state.visible = false;
  }
};

export default {
  namespaced: true,
  state,
  mutations
};
