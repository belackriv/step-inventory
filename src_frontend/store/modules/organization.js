import Api from '../../api/organization.js';

const api = new Api();

// actions
const actions = {
  fetch ({ commit, dispatch }) {
    api.getOrganization().then(organization => {
      dispatch('create', {
        data: organization
      });
    }).catch(error => {
      if (error.isAuthenticationError) {
        commit('modal/showModal', 'ModalLogin', { root: true });
      }
    });
  }
};

export default {
  actions
};
