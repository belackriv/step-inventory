"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminPartsEditView.hbs!";
import PartCategoryCollection from 'lib/inventory/models/partCategoryCollection.js';
import PartGroupCollection from 'lib/inventory/models/partGroupCollection.js';



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
    'partIdInput': 'input[name="partId"]',
    'partAltIdInput': 'input[name="partAltId"]',
    'descriptionInput': 'textarea[name="description"]',
    'isActiveInput': 'input[name="isActive"]',
    'partCategorySelect': 'select[name="partCategory"]',
    'partGroupSelect': 'select[name="partGroup"]',
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.partIdInput': 'partId',
    '@ui.partAltIdInput': 'partAltId',
    '@ui.descriptionInput': 'description',
    '@ui.isActiveInput': 'isActive',
    '@ui.partCategorySelect': {
      observe: 'partCategory',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', PartCategoryCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one or none...',
          value: null
        }
      }
    },
    '@ui.partGroupSelect': {
      observe: 'partGroup',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let collection = Radio.channel('data').request('collection', PartGroupCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one or none...',
          value: null
        }
      }
    },
  },
});
