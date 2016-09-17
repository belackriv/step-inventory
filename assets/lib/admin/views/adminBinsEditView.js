"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminBinsEditView.hbs!";
import OfficeCollection from 'lib/common/models/officeCollection.js';
import DepartmentCollection from 'lib/common/models/departmentCollection.js';
import PartCategoryCollection from 'lib/inventory/models/partCategoryCollection.js';
import BinTypeCollection from 'lib/inventory/models/binTypeCollection.js';
import BinCollection from 'lib/inventory/models/binCollection.js';
import binItemViewTpl from './binItemViewTpl.hbs!';
import FormChildListView from 'lib/common/views/formChildListView.js';

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
    'descriptionInput': 'textarea[name="description"]',
    'isActiveInput': 'input[name="isActive"]',
    'departmentSelect': 'select[name="department"]',
    'partCategorySelect': 'select[name="partCategory"]',
    'binTypeSelect': 'select[name="binType"]',
    'parentSelect': 'select[name="parent"]',
  },
  regions:{
    'children': '[data-ui-name="children"]'
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.partIdInput': 'partId',
    '@ui.partAltIdInput': 'partAltId',
    '@ui.descriptionInput': 'description',
    '@ui.isActiveInput': 'isActive',
    '@ui.departmentSelect': {
      observe: 'department',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', DepartmentCollection, {doFetch: false});
          let officeCollection = Radio.channel('data').request('collection', OfficeCollection, {doFetch: false});
          this.listenTo(officeCollection, 'add', (office)=>{
            collection.add(office.get('departments').models);
          });
          officeCollection.each((office)=>{
            collection.add(office.get('departments').models);
          });
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
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
    '@ui.binTypeSelect': {
      observe: 'binType',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let collection = Radio.channel('data').request('collection', BinTypeCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
    '@ui.parentSelect': {
      observe: 'parent',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', BinCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one or none...',
          value: null
        }
      }
    },
  },
  onRender(){
    this.showChildView('children', new FormChildListView({
      collection: this.model.get('children'),
      childTemplate: binItemViewTpl,
      noDelete: true
    }));
  }
});
