<template>
  <div class="box">
    <h3 class="title is-3 has-text-centered">Profile</h3>
    <form @submit.prevent="handleSubmit">
      <div class="field">
        <label class="label">Username:</label>
        <div class="control has-icons-left">
          <input name="username" class="input" placeholder="Username" v-model="profileUsername" />
          <span class="icon is-small is-left">
            <font-awesome-icon :icon="userIcon" />
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Email:</label>
        <div class="control has-icons-left">
          <input name="email" class="input" placeholder="Email" v-model="profileEmail" />
          <span class="icon is-small is-left">
            <font-awesome-icon :icon="emailIcon" />
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">First Name:</label>
        <div class="control">
          <input name="firstName" class="input" placeholder="First Name" v-model="profileFirstName" />
        </div>
      </div>
      <div class="field">
        <label class="label">Last Name:</label>
        <div class="control">
          <input name="lastName" class="input" placeholder="Last Name" v-model="profileLastName" />
        </div>
      </div>
      <div class="field is-grouped">
        <div class="control">
          <button type="submit" class="button is-primary" :disabled="disableButtons">Update</button>
        </div>
        <div class="control">
          <button type="button" class="button is-info is-outlined" :disabled="profileIsSyncing" @click="handleRevert">Revert</button>
        </div>
        <div class="control">
          <not-synced-alert v-bind:isNotSynced="isNotSynced"></not-synced-alert>
        </div>
        <div class="control">
          <span class="icon" :title="syncingTooltip">
            <font-awesome-icon v-if="profileIsSyncing" :icon="syncingIcon" spin />
          </span>
        </div>
      </div>
    </form>
  </div>

</template>

<script>
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import { faUser, faAt, faSync } from '@fortawesome/fontawesome-free-solid';
import NotSyncedAlert from './NotSyncedAlert.vue';

export default {
  name: 'Profile',
  computed: {
    profileIsSyncing () {
      return this.$store.getters['entities/myself/profileIsSyncing'];
    },
    syncingTooltip () {
      if (this.profileIsSyncing) {
        return 'Syncing...';
      }
      return null;
    },
    isNotSynced () {
      return !this.$store.getters['entities/myself/profileIsSynced'];
    },
    userIcon () {
      return faUser;
    },
    emailIcon () {
      return faAt;
    },
    syncingIcon () {
      return faSync;
    },
    myself () {
      return this.$store.getters['entities/myself/query']().first();
    },
    profileUsername: {
      get () {
        return this.myself.username;
      },
      set (value) {
        this.$store.commit('entities/myself/setIsSynced', false);
        this.$store.dispatch('entities/myself/update', {
          where: this.myself.id,
          data: { username: value }
        });
      }
    },
    profileEmail: {
      get () {
        return this.myself.email;
      },
      set (value) {
        this.$store.commit('entities/myself/setIsSynced', false);
        this.$store.dispatch('entities/myself/update', {
          where: this.myself.id,
          data: { email: value }
        });
      }
    },
    profileFirstName: {
      get () {
        return this.myself.firstName;
      },
      set (value) {
        this.$store.commit('entities/myself/setIsSynced', false);
        this.$store.dispatch('entities/myself/update', {
          where: this.myself.id,
          data: { firstName: value }
        });
      }
    },
    profileLastName: {
      get () {
        return this.myself.lastName;
      },
      set (value) {
        this.$store.commit('entities/myself/setIsSynced', false);
        this.$store.dispatch('entities/myself/update', {
          where: this.myself.id,
          data: { lastName: value }
        });
      }
    }
  },
  methods: {
    handleSubmit () {
      this.$store.dispatch('entities/myself/snapshot');
      this.$store.dispatch('entities/myself/syncProfile');
    },
    handleRevert () {
      this.$store.dispatch('entities/myself/revert');
      this.$store.commit('entities/myself/setIsSynced', true);
    }
  },
  created () {
    this.$store.dispatch('entities/myself/snapshot');
  },
  components: {
    FontAwesomeIcon,
    NotSyncedAlert
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="sass">

</style>
