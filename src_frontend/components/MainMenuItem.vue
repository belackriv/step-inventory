<template>
  <li>
    <router-link v-if="link.url" :to="link.url" v-bind:class="{ 'is-active': link.uiIsActive }">{{link.name}}</router-link>
  </li>
</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
// import { faUser, faCog } from '@fortawesome/fontawesome-free-solid';
// import { mapMutations } from 'vuex';

export default {
  name: 'MainMenuItem',
  props: ['link'],
  components: {
    FontAwesomeIcon
  },
  methods: {

  },
  created () {

  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="sass">

</style>
