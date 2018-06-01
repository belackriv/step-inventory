import Api from '../../api/myself.js';

const api = new Api();

// actions
const actions = {
  fetch ({ commit, dispatch }) {
    api.getMyself().then(myself => {
      dispatch('create', {
        data: myself
      });
    }).catch(error => {
      if (error.isAuthenticationError) {
        commit('modal/show', 'ModalLogin', { root: true });
      }
    });
  },
  login ({ commit, dispatch }, loginInfo) {
    api.login(loginInfo).then(myself => {
      dispatch('create', {
        data: myself
      });
      commit('modal/hide', null, { root: true });
    });
  }
};

export default {
  actions
};
