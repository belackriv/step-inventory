"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminCustomersEditView.hbs!";

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  ui: {
    'nameInput': 'input[name="name"]',
  },
  bindings: {
    '@ui.nameInput': 'name',
  },
});
