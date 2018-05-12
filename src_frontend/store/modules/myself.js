import MyselfApi from '../../api/myself.js';

const myselfApi = new MyselfApi();
// initial state
const state = {
  myself: {}
};

// getters
const getters = {
  myself: state => state.myself
};

// actions
const actions = {
  getMyself ({ commit }) {
    myselfApi.getMyself(myself => {
      commit('setMyself', myself);
    });
  }
};

// mutations
const mutations = {
  setMyself (state, myself) {
    state.myself = {...myself};
  }
};

export default {
  state,
  getters,
  actions,
  mutations
};
