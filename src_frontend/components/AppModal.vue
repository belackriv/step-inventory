<template>
  <div class="modal" v-bind:class="{ 'is-active': visible }" @keydown.esc="hideModal">
    <div class="modal-background" v-if="visible"></div>
    <div class="modal-content" v-if="visible" @click.self="hideModal">
      <component :is="component"></component>
    </div>
    <button class="modal-close is-large" aria-label="close" v-if="visible" @click.self="hideModal"></button>
  </div>
</template>

<script>
// eslint-disable-next-line
import Vue from 'vue';
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'AppModal',
  data () {
    return {
      component: null
    };
  },
  computed: {
    ...mapState({
      visible: state => state.modal.visible,
      modalComponentName: state => state.modal.componentName
    })
  },
  methods: {
    ...mapMutations({hideModal: 'modal/hide'})
  },
  watch: {
    modalComponentName (componentName) {
      if (!componentName) return;

      Vue.component(componentName, () => import(`./${componentName}`));

      this.component = componentName;
    }
  }
};
</script>

<style>

</style>
