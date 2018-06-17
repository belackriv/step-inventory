<template>
  <div class="columns is-mobile">
    <div class="column">
      <ul class="menu-list">
        <main-menu-item v-bind:item="item" v-for="item in activeItems" :key="item.id"></main-menu-item>
      </ul>
    </div>
    <div data-ui="closeSideMenu" class="column is-narrow is-hidden-desktop">
      <ul class="si-side-icon-list">
        <li><span class="icon"><span class="fa fa-navicon"></span></span></li>
        <li><span class="icon"><span data-ui="sideMenuDirectionIcons" class="fa fa-toggle-left"></span></span></li>
      </ul>
    </div>
  </div>
</template>

<script>
import MainMenuItem from './MainMenuItem.vue';

export default {
  name: 'MainMenu',
  computed: {
    activeItems () {
      const myself = this.$store.getters['entities/myself/query']().first();
      if (myself && myself.menuItems) {
        return myself.menuItems.filter(function (item) {
          return item.isActive;
        });
      }
      return [];
    }
  },
  components: {
    MainMenuItem
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
