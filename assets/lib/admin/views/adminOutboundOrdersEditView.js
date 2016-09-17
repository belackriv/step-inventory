"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminOutboundOrdersEditView.hbs!";
import CustomerCollection from 'lib/accounting/models/customerCollection.js';

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  ui: {
    'descriptionInput': 'textarea[name="description"]',
    'isVoidInput': 'input[name="isVoid"]',
    'customerSelect': 'select[name="customer"]',
  },
  bindings: {
    '@ui.descriptionInput': 'description',
    '@ui.isVoidInput': 'isVoid',
    '@ui.customerSelect': {
      observe: 'customer',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', CustomerCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
});
