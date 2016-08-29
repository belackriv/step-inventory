"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminDepartmentsEditView.hbs!";
import OfficeCollection from 'lib/common/models/officeCollection.js';

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
    'officeSelect': 'select[name="office"]',
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.officeSelect': {
      observe: 'office',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          return Radio.channel('data').request('collection', OfficeCollection);
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
});
