"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminContactsEditView.hbs!";

export default Marionette.View.extend({
  template: viewTpl,
  behaviors(){
    return {
      'Stickit': {},
      'ShowNotSynced': {},
      'SetNotSynced': {},
      'SaveCancelDelete': {
        postDelete: this.options.postDelete
      },
    }
  },
  ui: {
    'firstNameInput': 'input[name="firstName"]',
    'lastNameInput': 'input[name="lastName"]',
    'emailAddressInput': 'input[name="emailAddress"]',
    'phoneNumberInput': 'input[name="phoneNumber"]',
    'positionInput': 'input[name="position"]',
  },
  bindings: {
    '@ui.firstNameInput': 'firstName',
    '@ui.lastNameInput': 'lastName',
    '@ui.emailAddressInput': 'emailAddress',
    '@ui.phoneNumberInput': 'phoneNumber',
    '@ui.positionInput': 'position',
  },
});
