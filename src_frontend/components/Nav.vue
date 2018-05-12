<template>
  <nav class="navbar has-shadow si-nav">
    <div class="navbar-brand">
      <a href="/" class="navbar-item si-brand"><span>Step</span>Inventory<span>.</span></a>
      <span class="navbar-item si-slogan">Track It Easy©</span>
    </div>
    <div class="navbar-menu">
      <div class="navbar-start si-nav-expanded">
        <div class="navbar-item is-expanded">
          <h1 class="is-centered title is-1">{{organization.name}}</h1>
        </div>
      </div>
      <div class="navbar-end si-nav-expanded">
        <div class="navbar-item">
          <a class="icon si-nav-link" href="/profile" :title="profileTitle">
            <font-awesome-icon :icon="userIcon" />
          </a>
          <a class="icon si-nav-link" href="/account" title="Edit Account Info">
            <font-awesome-icon :icon="accountIcon" />
          </a>
        </div>
        <div class="navbar-item">
          <a class="button" href="/logout" data-default-nav-action="true">Logout</a>
          <span id="loading-icon-container" class="icon"></span>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faUser, faCog } from '@fortawesome/fontawesome-free-solid';
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'Nav',
  data () {
    return {
      organization: {
        name: 'Test'
      }
    };
  },
  computed: {
    profileTitle () {
      return 'Edit ' + this.myself.username + '\'s Profile';
    },
    userIcon () {
      return faUser;
    },
    accountIcon () {
      return faCog;
    },
    ...mapGetters({
      myself: 'myself'
    })
  },
  components: {
    FontAwesomeIcon
  },
  methods: mapActions([

  ]),
  created () {
    this.$store.dispatch('getMyself');
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
