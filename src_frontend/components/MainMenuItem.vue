<template>
  <li>
    <router-link v-if="item.menuLink.url" :to="item.menuLink.url">{{item.menuLink.name}}</router-link>
    <span v-else>{{item.menuLink.name}}</span>
    <ul>
      <main-menu-item v-bind:item="item" v-for="item in item.children" :key="item.id"></main-menu-item>
    </ul>
  </li>
</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
// import { faUser, faCog } from '@fortawesome/fontawesome-free-solid';
// import { mapMutations } from 'vuex';

export default {
  name: 'MainMenuItem',
  props: ['item'],
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
