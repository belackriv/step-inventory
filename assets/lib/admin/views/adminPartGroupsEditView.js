"use strict";

import Marionette from 'marionette';

import viewTpl from  "./adminPartGroupsEditView.hbs!";

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
    'isActiveInput': 'input[name="isActive"]',
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.isActiveInput': 'isActive',
  },
});
