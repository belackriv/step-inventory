"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminAddressesEditView.hbs!";

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
    'streetInput': 'input[name="street"]',
    'unitInput': 'input[name="unit"]',
    'cityInput': 'input[name="city"]',
    'stateInput': 'input[name="state"]',
    'postalCodeInput': 'input[name="postalCode"]',
    'countryInput': 'input[name="country"]',
    'typeInput': 'select[name="type"]',
    'noteInput': 'input[name="note"]',
  },
  bindings: {
    '@ui.streetInput': 'street',
    '@ui.unitInput': 'unit',
    '@ui.cityInput': 'city',
    '@ui.stateInput': 'state',
    '@ui.postalCodeInput': 'postalCode',
    '@ui.countryInput': 'country',
    '@ui.typeInput': 'type',
    '@ui.noteInput': 'note',
  },
});
