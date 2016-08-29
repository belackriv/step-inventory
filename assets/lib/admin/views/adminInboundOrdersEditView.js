"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminInboundOrdersEditView.hbs!";
import ClientCollection from 'lib/accounting/models/clientCollection.js';

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
    'clientSelect': 'select[name="client"]',
  },
  bindings: {
    '@ui.descriptionInput': 'description',
    '@ui.isVoidInput': 'isVoid',
    '@ui.clientSelect': {
      observe: 'client',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', ClientCollection);
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
