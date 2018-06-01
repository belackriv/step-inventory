<template>
  <div class="si-main">
    <menu class="mobile-menu si-side-menu is-hidden-desktop">
      <main-menu />
    </menu>
    <div class="columns is-mobile">
      <menu class="column menu is-narrow si-side-menu is-hidden-touch">
        <main-menu />
      </menu>
      <div class="column is-narrow is-hidden-desktop si-side-menu-toggle">
        <ul class="si-side-icon-list">
          <li><span class="icon"><font-awesome-icon :icon="navIcon"/></span></li>
          <li><span class="icon"><font-awesome-icon :icon="toggleRightIcon" /></span></li>
        </ul>
      </div>
      <div class="column">
        <section class="section">
          <div class="container is-fluid" id="main-section">
            <router-view name="MainSection"></router-view>
          </div>
        </section>
      </div>
      <div class="column is-narrow si-help is-hidden-touch" id="help-panel"></div>
    </div>
  </div>
</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faBars } from '@fortawesome/fontawesome-free-solid';
import { faCaretSquareRight } from '@fortawesome/fontawesome-free-regular';
import MainMenu from './MainMenu.vue';

export default {
  name: 'AppMain',
  computed: {
    navIcon () {
      return faBars;
    },
    toggleRightIcon () {
      return faCaretSquareRight;
    }
  },
  components: {
    FontAwesomeIcon,
    MainMenu
  }
};
</script>

<style>

</style>
