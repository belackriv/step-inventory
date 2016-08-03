"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import MenuLinkCollection from 'lib/common/models/menuLinkCollection.js';
import OfficeCollection from 'lib/common/models/officeCollection.js';
import MenuItemCollection from 'lib/common/models/menuItemCollection.js';
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
    'children': '[data-ui-name="children"]'
  },
  ui: {
    'positionInput': 'input[name="position"]',
    'isActiveInput': 'input[name="isActive"]',
    'menuLinkSelect': 'select[name="menuLink"]',
    'departmentSelect': 'select[name="department"]',
    'parentSelect': 'select[name="parent"]'
  },
  bindings: {
    '@ui.positionInput': 'position',
    '@ui.isActiveInput': 'isActive',
    '@ui.menuLinkSelect': {
      observe: 'menuLink',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', MenuLinkCollection, {fetchAll: true});
          return collection;
        },
      }
    },
    '@ui.departmentSelect': {
      observe: 'department',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let departments = [];
          let collection = Radio.channel('data').request('collection', OfficeCollection);
          collection.each((office)=>{
            office.get('departments').each((department)=>{
                departments.push(department);
            });
          });
          return departments;
        },
        defaultOption: {
          label: 'Choose one or none...',
          value: null
        }
      }
    },
    '@ui.parentSelect': {
      observe: 'parent',
      useBackboneModels: true,
      selectOptions:{
        labelPath(item){
          let label = '#'+item.id;
          if(item.get('menuLink')){
            label += ' - '+item.get('menuLink').get('name');
          }
          if(item.get('department')){
            label += ' ('+item.get('department').get('name')+')';
          }
          return label;
        },
        collection(){
          let collection = Radio.channel('data').request('collection', MenuItemCollection, {doFetch: false});
          collection.fetch({data:{disable_pagination: true}}).done(()=>{
            collection.remove(this.model);
          });
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
      childTemplate: menuItemItemViewTpl,
      noDelete: true
    }));
  }
});
