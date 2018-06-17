import Vue from 'vue';
import Vuex from 'vuex';
import VuexORM from '@vuex-orm/core';
import createLogger from 'vuex/dist/logger';
import database from './database.js';

import login from './modules/login.js';
import modal from './modules/modal.js';
import syncing from './modules/syncing.js';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  modules: {login, modal, syncing},
  strict: debug,
  plugins: debug ? [createLogger(), VuexORM.install(database)] : [VuexORM.install(database)]
});
