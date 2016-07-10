"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';
import MenuItemCollection from 'lib/common/models/menuItemCollection.js';
import MenuLinkCollection from 'lib/common/models/menuLinkCollection.js';
import viewTpl from  "./adminMenuItemsEditView.hbs!";
import FormChildListView from 'lib/common/views/formChildListView.js';
import menuItemItemViewTpl from './menuItemItemView.hbs!'

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  regions:{
    'roles': '[data-ui-name="roles"]'
  },
  ui: {
    'positionInput': 'input[name="position"]',
    'isActiveInput': 'input[name="isActive"]',
    'menuLinkSelect': 'select[name="defaultDepartment"]',
    'childMenuItemSelect': 'select[name="menuItems"]',
    'addChildButton': 'button[name="addChild"]',
  },
  events: {
    'click @ui.addChildButton': 'onAddChildButtonClicked'
  },
  onAddChildButtonClicked(event){
    event.preventDefault();
    if( this.model.get('selectedMenuItem') && !this.model.hasChild(this.model.get('selectedMenuItem')) ){
      let userRole = new UserRoleModel({
        user: this.model,
        role: this.model.get('selectedRole')
      });
    }
  },
  bindings: {
    '@ui.positionInput': 'position',
    '@ui.isActiveInput': 'isActive',
    '@ui.menuLinkSelect': {
      observe: 'menuLink',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          return Radio.channel('data').request('collection', MenuLinkCollection, {fetchAll: true});
        },
      }
    },
    '@ui.childMenuItemSelect': {
      observe: 'selectedMenuItem',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.id',
        collection() {
          return Radio.channel('data').request('collection', MenuItemCollection, {fetchAll: true});
        },
      }
    },
  },
  onRender(){
   this.showChildView('roles', new FormChildListView({
      collection: this.model.get('userRoles'),
      childTemplate: menuItemItemViewTpl
    }));
  }
});
