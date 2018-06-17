import Api from '../../api/myself.js';

const api = new Api();

// initial state
const state = {
  profileIsSynced: true,
  profileIsSyncing: false,
  loginIsSyncing: false,
  snapshot: {
    id: null,
    username: null,
    email: null,
    firstName: null,
    lastName: null
  }
};

const getters = {
  profileIsSynced (state) {
    return state.profileIsSynced;
  },
  profileIsSyncing (state) {
    return state.profileIsSyncing;
  },
  loginIsSyncing (state) {
    return state.loginIsSyncing;
  }
};

// mutations
const mutations = {
  setIsSynced (state, value) {
    state.profileIsSynced = value;
  },
  setIsSyncing (state, value) {
    state.profileIsSyncing = value;
  }
};

// actions
const actions = {
  fetch ({ commit, dispatch, state }) {
    commit('syncing/inc', null, { root: true });
    api.getMyself().then(myself => {
      commit('syncing/dec', null, { root: true });
      dispatch('create', {
        data: myself
      });
      if (!state.snapshot.id) {
        state.snapshot = {
          id: myself.id,
          username: myself.username,
          email: myself.email,
          firstName: myself.firstName,
          lastName: myself.lastName
        };
      }
    }).catch(error => {
      commit('syncing/dec', null, { root: true });
      if (error.isAuthenticationError) {
        commit('modal/show', 'ModalLogin', { root: true });
      }
    });
  },
  snapshot ({state, commit}) {
    const myself = this.getters['entities/myself/query']().first();
    if (myself && myself.id) {
      state.snapshot = {
        id: myself.id,
        username: myself.username,
        email: myself.email,
        firstName: myself.firstName,
        lastName: myself.lastName
      };
    }
  },
  revert ({state, dispatch}) {
    const myself = this.getters['entities/myself/query']().first();
    dispatch('update', {
      where: myself.id,
      data: {
        username: state.snapshot.username,
        email: state.snapshot.email,
        firstName: state.snapshot.firstName,
        lastName: state.snapshot.lastName
      }
    });
  },
  syncProfile ({ state, commit }) {
    const myself = this.getters['entities/myself/query']().first();
    state.profileIsSyncing = true;
    commit('syncing/inc', null, { root: true });
    api.syncProfile({
      username: myself.username,
      email: myself.email,
      firstName: myself.firstName,
      lastName: myself.lastName
    }).then(myself => {
      state.profileIsSyncing = false;
      state.profileIsSynced = true;
      commit('syncing/dec', null, { root: true });
    }).catch(error => {
      state.profileIsSyncing = false;
      commit('syncing/dec', null, { root: true });
      // more?
      if (error.isAuthenticationError) {
        commit('modal/show', 'ModalLogin', { root: true });
      }
    });
  },
  login ({ state, commit, dispatch }, loginInfo) {
    state.loginIsSyncing = true;
    commit('syncing/inc', null, { root: true });
    api.login(loginInfo).then(myself => {
      dispatch('create', {
        data: myself
      });
      state.loginIsSyncing = false;
      commit('syncing/dec', null, { root: true });
      commit('modal/hide', null, { root: true });
    }).catch(error => {
      state.loginIsSyncing = false;
      commit('syncing/dec', null, { root: true });
      // more
      if (error.isAuthenticationError) {
        commit('modal/show', 'ModalLogin', { root: true });
      }
    });
  }
};

export default {
  state,
  getters,
  mutations,
  actions
};
