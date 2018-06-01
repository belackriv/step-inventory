<template>
  <div>
    {{profileTitle}} Test...
  </div>

</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faUser, faCog } from '@fortawesome/fontawesome-free-solid';
// import { mapMutations } from 'vuex';

export default {
  name: 'Profile',
  computed: {
    profileTitle () {
      return this.myself.username + '\'s Profile';
    },
    userIcon () {
      return faUser;
    },
    accountIcon () {
      return faCog;
    },
    myself () {
      return this.$store.getters['entities/myself/query']().with('organization').first();
    }
  },
  components: {
    FontAwesomeIcon
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="sass">

</style>
