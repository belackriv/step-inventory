"use strict";

import _ from 'underscore';
import Backbone from 'backbone'
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import menuSelectionTpl from './menuSelectionView.hbs!';

import OfficeCollection from 'lib/common/models/officeCollection.js';
import DeptartmentCollection from 'lib/common/models/departmentCollection.js';
import MenuItemCollection from 'lib/common/models/menuItemCollection.js';

export default Marionette.View.extend({
	initialize(options){
		this.officeCollection = Radio.channel('data').request('collection', OfficeCollection);
		this.departmentCollection = Radio.channel('data').request('collection', DeptartmentCollection, {doFetch:false});
	},
	behaviors:{
		Stickit: {}
	},
  template: menuSelectionTpl,
  ui: {
    officeSelect: "#menu-office-select",
    departmentSelect: "#menu-department-select"
  },
  modelEvents: {
  	"change:office": "onOfficeChange",
    "change:department": "onDepartmentChange"
  },
  bindings: {
    '@ui.officeSelect': {
      observe: 'office',
      selectOptions:{
        labelPath: 'name',
        collection: 'this.officeCollection',
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
    '@ui.departmentSelect': {
      observe: 'department',
      selectOptions:{
        labelPath: 'name',
        collection: 'this.departmentCollection',
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    }
  },
  onOfficeChange(){
  	if(this.model.get('office')){
  		this.departmentCollection.reset(this.model.get('office').departments);
  	}
  },
  onDepartmentChange(){
  	if(this.model.get('department')){
	  	let menuItemsCollection = new MenuItemCollection(this.model.get('department').menuItems);
	    Radio.channel('app').trigger('change:menuItems', menuItemsCollection);
	  }
  },
});