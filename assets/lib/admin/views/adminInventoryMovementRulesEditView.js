"use strict";

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminInventoryMovementRulesEditView.hbs!";
import PropertyArrayListView from 'lib/common/views/propertyArrayListView.js';
import RoleCollection from 'lib/common/models/roleCollection.js';
import BinTypeCollection from 'lib/inventory/models/binTypeCollection.js';

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
    'roleSelect': 'select[name="role"]',
    'binTypeSelect': 'select[name="binType"]',
    'restrictionSelect': 'select[name="restriction"]',
    'addRestrictionButton': 'button[name="addRestriction"]',
  },
  regions:{
    'restrictions': '[data-ui-name="restrictions"]'
  },
   events: {
    'click @ui.addRestrictionButton': 'onAddRestrictionButtonClicked'
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.descriptionInput': 'description',
    '@ui.isActiveInput': 'isActive',
    '@ui.roleSelect': {
      observe: 'role',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let roles = Radio.channel('data').request('collection', RoleCollection);
          return roles;
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
          let collection = Radio.channel('data').request('collection', BinTypeCollection);
          return collection;
        },
        defaultOption: {
          label: 'Choose one or none...',
          value: null
        }
      }
    },
    '@ui.restrictionSelect': {
      observe: 'selectedRestriction',
      selectOptions:{
        collection() {
          return this.model.restrictionList;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
  onAddRestrictionButtonClicked(event){
    event.preventDefault();
    if( this.model.get('selectedRestriction') && !this.model.hasRestriction(this.model.get('selectedRestriction')) ){
      this.model.addRestriction(this.model.get('selectedRestriction'));
    }
  },
  onRender(){
    this.showChildView('restrictions', new PropertyArrayListView({
      model: this.model,
      propertyName: 'restrictions',
      dictionary: this.model.restrictionList
    }));
  }
});
