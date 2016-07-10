"use strict";

import _ from 'underscore';
import Backbone from 'backbone'
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import menuSelectionTpl from './menuSelectionView.hbs!';

import OfficeCollection from 'lib/common/models/officeCollection.js';
import DepartmentCollection from 'lib/common/models/departmentCollection.js';
import MenuItemCollection from 'lib/common/models/menuItemCollection.js';

export default Marionette.View.extend({
	initialize(options){
		this.officeCollection = Radio.channel('data').request('collection', OfficeCollection);
		this.departmentCollection = new DepartmentCollection();
    this.setupDefaultDepartMent();
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
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection: 'this.officeCollection',
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
    '@ui.departmentSelect': {
      observe: 'department',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
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
      /*let departments = this.model.get('office').get('departments').map((department)=>{
        return department.attributes;
      });*/
  		this.departmentCollection.reset( this.model.get('office').get('departments').models);
  	}
  },
  onDepartmentChange(){
  	if(this.model.get('department')){
      /*let menuItems = this.model.get('department').get('menuItems').map((menuItem)=>{
        return menuItem.attributes;
      });*/
	  	let menuItemsCollection = new MenuItemCollection(this.model.get('department').get('menuItems').models);
	    Radio.channel('app').trigger('change:menuItems', menuItemsCollection);
      let myself = Radio.channel('data').request('myself');
      myself.set('currentDepartment', this.model.get('department'));
      myself.save();
	  }
  },
  setupDefaultDepartMent(){
    let myself = Radio.channel('data').request('myself');
    if(myself.get('currentDepartment')){
      this.setupOfficeCollectionListener(myself)
    }else{
      this.listenToOnce(myself, 'change:currentDepartment', ()=>{
        this.setupOfficeCollectionListener(myself)
      });
    }
  },
  setupOfficeCollectionListener(myself){
    if(this.officeCollection.length > 0){
      this.setOfficeAndDepartmentFromMyself(myself);
    }else{
      this.listenToOnce(this.officeCollection, 'update', ()=>{
        this.setOfficeAndDepartmentFromMyself(myself);
      });
    }
  },
  setOfficeAndDepartmentFromMyself(myself){
    this.model.set('office', myself.get('currentDepartment').get('office'));
    this.model.set('department', myself.get('currentDepartment'));
  }
});