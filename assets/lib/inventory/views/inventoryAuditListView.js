'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryAuditListView.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import inventoryAuditListTableLayoutTpl from './inventoryAuditListTableLayoutTpl.hbs!';
import inventoryAuditRowTpl from './inventoryAuditRowTpl.hbs!';
import InventoryAuditCollection from '../models/inventoryAuditCollection.js';
import InventoryAuditModel from '../models/inventoryAuditModel.js';
import InventoryAuditSetupView from './inventoryAuditSetupView.js';
import InventoryAuditView from './inventoryAuditView.js';


export default Marionette.View.extend({
  initialize(options){
    this.listenTo(Radio.channel('inventory'), 'resume:audit', this.resume);
    this.listenTo(Radio.channel('inventory'), 'change:audit', this.refreshList);
  },
  template: viewTpl,
  regions: {
    list: '[data-region="list"]'
  },
  ui: {
    'auditButton': 'button[name="audit"]',
  },
  events: {
    'click @ui.auditButton': 'audit'
  },
  childViewEvents: {
    'button:click': 'buttonClicked',
    'link:click': 'linkClicked'
  },
  onRender(){
    this.showList();
  },
  showList(){
    let inventoryAuditCollection = Radio.channel('data').request('collection', InventoryAuditCollection, {doFetch: false});
    this.listView = new SearchableListLayoutView({
      collection: inventoryAuditCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','forBin.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryAuditListTableLayoutTpl,
      entityRowTpl: inventoryAuditRowTpl,
      colspan: 5,
    });
    this.showChildView('list', this.listView);
    Radio.channel('app').trigger('navigate', inventoryAuditCollection.url(), {trigger: false});
  },
  refreshList(){
    this.listView.search();
  },
  buttonClicked(childView, args){
    let action = args.button.getAttribute('name');
    this[action](args.model);
  },
  linkClicked(childView, args){
    let reportView = new InventoryAuditView({
      model: args.model
    });
    Radio.channel('app').trigger('show:view', reportView);
  },
  audit(event){
    event.preventDefault();
    var options = {
      title: 'Audit Inventory',
      width: '400px'
    };
    let inventoryAudit = new InventoryAuditModel();
    let view = new InventoryAuditSetupView({
      model: inventoryAudit
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  resume(model){
    let auditView = new InventoryAuditView({
      model: model
    });
    Radio.channel('app').trigger('show:view', auditView);
  },
});