<template>
  <div id="app">
    <router-view name="Nav" />
    <router-view name="AppMain" />
    <router-view name="Footer" />
    <app-modal></app-modal>
  </div>
</template>

<script>
import AppModal from './components/AppModal.vue';

export default {
  name: 'App',
  components: {
    AppModal
  }
};
</script>

<style>

</style>
