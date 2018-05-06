"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminHelpTopicsEditView.hbs!";

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
    'headingInput': 'input[name="heading"]',
    'contentInput': 'textarea[name="content"]',
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.headingInput': 'heading',
    '@ui.contentInput': 'content',
  },
});
