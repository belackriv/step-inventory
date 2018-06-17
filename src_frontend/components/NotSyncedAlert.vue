<template>
  <span class="icon not-synced-alert" :title="notSyncedTooltip">
    <font-awesome-icon v-if="isNotSynced" :icon="alertIcon" />
  </span>
</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faExclamationTriangle } from '@fortawesome/fontawesome-free-solid';

export default {
  name: 'NotSyncedAlert',
  props: ['isNotSynced'],
  computed: {
    alertIcon () {
      return faExclamationTriangle;
    },
    notSyncedTooltip () {
      if (this.isNotSynced) {
        return 'This Item is not synced with the server.';
      }
      return null;
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
