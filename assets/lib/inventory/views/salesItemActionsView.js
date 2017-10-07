'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './salesItemActionsView.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import salesItemListTableLayoutTpl from './salesItemListTableLayoutTpl.hbs!';
import salesItemRowTpl from './salesItemRowTpl.hbs!';


import InventorySalesItemMassEditActionView from './inventorySalesItemMassEditActionView.js';
import InventorySalesItemMassSelectionActionView from './inventorySalesItemMassSelectionActionView.js';

import SalesItemCollection from '../models/salesItemCollection.js';

export default Marionette.View.extend({
  initialize(options){
    this.selectedCollection = Radio.channel('inventory').request('get:isSelected:salesItem');
    this.listenTo(this.selectedCollection, 'update', this.updateSelectedCount);
    this.listenTo(Radio.channel('inventory'), 'refresh:list:salesItem', this.refreshList);
  },
  template: viewTpl,
  regions: {
    list: '[data-region="list"]'
  },
  ui: {
    'massEditButton': 'button[name="massEdit"]',
    'massSelectButton': 'button[name="massSelect"]',
    'selectedCountMsg': 'div[data-ui-name="selectedCount"]',
    'clearSelectionButton': 'button[data-ui-name="clearSelection"]',
  },
  events: {
    'click @ui.massEditButton': 'massEdit',
    'click @ui.massSelectButton': 'massSelect',
    'click @ui.clearSelectionButton': 'clearSelection',
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'button:click': 'buttonClicked',
    'link:click': 'linkClicked',
  },
  onRender(){
    this.showList();
    this.updateSelectedCount();
  },
  showList(){
    let salesItemCollection = Radio.channel('data').request('collection', SalesItemCollection, {doFetch: false});
    this.listView = new SearchableListLayoutView({
      collection: salesItemCollection,
      listLength: 20,
      searchPath: ['id', 'outboundOrder.label', 'bin.name', 'sku.name', 'sku.number', 'sku.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: salesItemListTableLayoutTpl,
      entityRowTpl: salesItemRowTpl,
      colspan: 8,
    });
    this.showChildView('list', this.listView);
    Radio.channel('app').trigger('navigate', salesItemCollection.url(), {trigger: false});
  },
  updateSelectedCount(){
    if(this.selectedCollection.length > 0){
      this.ui.selectedCountMsg.parent().show();
      this.ui.selectedCountMsg.text(this.selectedCollection.length+' Sales Items Selected');
    }else{
      this.ui.selectedCountMsg.parent().hide();
      this.ui.selectedCountMsg.text('');
    }
  },
  clearSelection(){
    let salesItem = this.selectedCollection.toArray();
    _.invoke(salesItem, 'set', 'isSelected', false);
    this.selectedCollection.reset();
    this.updateSelectedCount();
  },
  refreshList(){
    this.listView.search();
  },
  selectModel(childView, args){
    this.triggerMethod('select:model', args);
  },
  buttonClicked(childView, args){
    let action = args.button.getAttribute('name');
    this[action](args.model);
  },
  linkClicked(childView, args){
    let methodName = args.link.dataset.uiLink+'LinkClicked';
    this[methodName](args.model);
  },
  selectAllToggleValue: false,
  toggleSelectAll(){
    this.selectAllToggleValue = !this.selectAllToggleValue;
    let collection = this.listView.getCurrentCollection();
    if(collection){
      collection.invoke('set','isSelected', this.selectAllToggleValue);
   }
  },
  massEdit(event){
    event.preventDefault();
    var options = {
      title: 'Mass Edit SalesItem',
      width: '400px'
    };
    let view = new InventorySalesItemMassEditActionView();
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  massSelect(event){
    event.preventDefault();
     var options = {
      title: 'Mass Select SalesItem',
      width: '400px'
    };
    let view = new InventorySalesItemMassSelectionActionView();
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  outboundOrderLinkClicked(model){
    this.triggerMethod('show:outboundOrder', model.get('outboundOrder'));
  },
  binLinkClicked(model){
    this.triggerMethod('show:bin', model.get('bin'));
  },
  showCard(model){
    this.triggerMethod('show:card', model);
  }
});