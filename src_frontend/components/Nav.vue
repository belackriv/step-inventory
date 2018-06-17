<template>
  <nav class="navbar has-shadow si-nav">
    <div class="navbar-brand">
      <a href="/" class="navbar-item si-brand"><span>Step</span>Inventory<span>.</span></a>
      <span class="navbar-item si-slogan">Track It Easy©</span>
    </div>
    <div class="navbar-menu">
      <div class="navbar-start si-nav-expanded">
        <div class="navbar-item is-expanded">
          <h1 class="is-centered title is-1" v-if="myself.organization">{{myself.organization.name}}</h1>
          <h1 class="is-centered title is-1" v-else>&nbsp;</h1>
        </div>
      </div>
      <div class="navbar-end si-nav-expanded">
        <div class="navbar-item">
          <span class="icon" :title="syncingTooltip">
            <font-awesome-icon v-if="isSyncing" :icon="syncingIcon" spin />
          </span>
          <router-link class="icon si-nav-link" to="/profile" :title="profileTitle" v-if="myself.id">
            <font-awesome-icon :icon="userIcon" />
          </router-link>
          <router-link class="icon si-nav-link" to="/account" title="Edit Account Info" v-if="myself.id">
            <font-awesome-icon :icon="accountIcon" />
          </router-link>
        </div>
        <div class="navbar-item">
          <button type="button" class="button" href="/logout" v-if="myself.id">Logout</button>
          <button type="button" class="button" href="/login" v-else @click="showModal('ModalLogin')">Login</button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faUser, faCog, faSync } from '@fortawesome/fontawesome-free-solid';
import { mapMutations, mapState } from 'vuex';

export default {
  name: 'Nav',
  computed: {
    ...mapState({
      isSyncing: state => state.syncing.isSyncing
    }),
    profileTitle () {
      return 'Edit ' + this.myself.username + '\'s Profile';
    },
    userIcon () {
      return faUser;
    },
    accountIcon () {
      return faCog;
    },
    syncingIcon () {
      return faSync;
    },
    myself () {
      return this.$store.getters['entities/myself/query']().with('organization').first();
    },
    syncingTooltip () {
      if (this.isSyncing) {
        return 'Syncing...';
      }
      return null;
    }
  },
  components: {
    FontAwesomeIcon
  },
  methods: {
    ...mapMutations({showModal: 'modal/show'})
  },
  created () {
    this.$store.dispatch('entities/myself/create', {
      data: { id: null }
    });
    this.intervalId = setInterval(() => {
      this.$store.dispatch('entities/myself/fetch');
    }, 60000);
    this.$store.dispatch('entities/myself/fetch');
  },
  destroyed () {
    clearInterval(this.intervalId);
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="sass">
  .si-nav-expanded
    flex-grow: 1
  .navbar-item.is-expanded, .title
    width: 100%
    text-align: center

</style>
