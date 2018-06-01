<template>
  <div class="card si-rounded si-modal-border">
    <div class="card-hearder">
      <p class="card-header-title is-centered">
        Please Login
      </p>
    </div>
    <div class="card-content">
      <div class="field">
        <p class="control has-icons-left has-icons-right">
          <input class="input" type="username" placeholder="Username" v-model="loginUsername">
          <span class="icon is-small is-left">
            <font-awesome-icon :icon="userIcon" />
          </span>
        </p>
      </div>
      <div class="field">
        <p class="control has-icons-left">
          <input class="input" type="password" placeholder="Password" v-model="loginPassword">
          <span class="icon is-small is-left">
            <font-awesome-icon :icon="passwordIcon" />
          </span>
        </p>
      </div>
    </div>
    <div class="card-footer">
      <span class="card-footer-item">
        <button class="button is-primary" type="button" @click="login">Login</button>
      </span>
      <span class="card-footer-item">
        <button class=" button" type="button" @click="hideModal">Cancel</button>
      </span>
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faUser, faKey } from '@fortawesome/fontawesome-free-solid';

export default {
  name: 'ModalLogin',
  components: {
    FontAwesomeIcon
  },
  computed: {
    userIcon () {
      return faUser;
    },
    passwordIcon () {
      return faKey;
    },
    loginUsername: {
      get () {
        return this.$store.state.login.username;
      },
      set (value) {
        this.$store.commit('login/updateUsername', value);
      }
    },
    loginPassword: {
      get () {
        return this.$store.state.login.password;
      },
      set (value) {
        this.$store.commit('login/updatePassword', value);
      }
    }
  },
  methods: {
    ...mapMutations({
      hideModal: 'modal/hide'
    }),
    login () {
      this.$store.dispatch('entities/myself/login', this.$store.state.login);
    }
  }
};
</script>

<style lang="sass" scoped>
@import '~bulma/bulma'

.si-rounded
  border-radius: 1em
.si-modal-border
  border: 3px solid $primary
</style>
