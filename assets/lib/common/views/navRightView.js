"use strict";

import _ from 'underscore';
import Backbone from 'backbone'
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './navRightView.hbs!';

import OfficeCollection from 'lib/common/models/officeCollection.js';
import DepartmentCollection from 'lib/common/models/departmentCollection.js';
import MenuItemCollection from 'lib/common/models/menuItemCollection.js';

import LoadingView from 'lib/common/views/loadingView.js';

export default Marionette.View.extend({
  initialize(options){
    this.officeCollection = Radio.channel('data').request('collection', OfficeCollection);
    this.departmentCollection = new DepartmentCollection();
    this.setupDefaultDepartMent();
  },
  className: 'nav-right',
  behaviors:{
    Stickit: {}
  },
  template: viewTpl,
  regions: {
    loading: "#loading-icon-container",
  },
  ui: {
    officeSelect: "#menu-office-select",
    departmentSelect: "#menu-department-select",
    "click a": "navigate"
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
  onRender(){
    this.listenTo(Radio.channel('app'), 'loading:show', this._showLoading);
    this.listenTo(Radio.channel('app'), 'loading:hide', this._hideLoading);
  },
  onOfficeChange(){
    if(this.model.get('office')){
      this.departmentCollection.reset( this.model.get('office').get('departments').models);
    }
  },
  onDepartmentChange(){
    if(this.model.get('department')){
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
    if(myself.get('currentDepartment') && myself.get('currentDepartment') instanceof Backbone.Model){
      this.model.set('office', myself.get('currentDepartment').get('office'));
      this.model.set('department', myself.get('currentDepartment'));
    }
  },
  navigate: function(e){
    if(e.currentTarget.dataset.defaultNavAction !== 'true'){
      e.preventDefault();
      e.stopPropagation();
      Radio.channel('app').trigger('navigate', e.currentTarget.getAttribute('href'));
    }
  },
  _showLoading(){
    this.showChildView('loading', new LoadingView());
  },
  _hideLoading(){
    this.getRegion('loading').empty();
  },
});