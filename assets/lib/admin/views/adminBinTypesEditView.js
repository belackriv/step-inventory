"use strict";

import Backbone from 'backbone';
import Marionette from 'marionette';

import viewTpl from  "./adminBinTypesEditView.hbs!";
import PropertyArrayListView from 'lib/common/views/propertyArrayListView.js';

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
    'behavoirSelect': 'select[name="behavoir"]',
    'addBehavoirButton': 'button[name="addBehavoir"]',
  },
  regions:{
    'behavoirs': '[data-ui-name="behavoirs"]'
  },
   events: {
    'click @ui.addBehavoirButton': 'onAddBehavoirButtonClicked'
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.descriptionInput': 'description',
    '@ui.isActiveInput': 'isActive',
    '@ui.behavoirSelect': {
      observe: 'selectedBehavoir',
      selectOptions:{
        collection() {
          return this.model.behavoirList;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
  onAddBehavoirButtonClicked(event){
    event.preventDefault();
    if( this.model.get('selectedBehavoir') && !this.model.hasBehavoir(this.model.get('selectedBehavoir')) ){
      this.model.addBehavoir(this.model.get('selectedBehavoir'));
    }
  },
  onRender(){
    this.showChildView('behavoirs', new PropertyArrayListView({
      model: this.model,
      propertyName: 'behavoirs',
      dictionary: this.model.behavoirList
    }));
  }
});
