'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './binSkuCountView.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import binSkuCountListTableLayoutTpl from './binSkuCountListTableLayoutTpl.hbs!';
import binSkuCountRowTpl from './binSkuCountRowTpl.hbs!';
import InventorySkuAdjustmentActionView from './inventorySkuAdjustmentActionView.js';
import InventorySkuMovementActionView from './inventorySkuMovementActionView.js';

import BinSkuCountCollection from '../models/binSkuCountCollection.js';
import InventorySkuAdjustmentModel from '../models/inventorySkuAdjustmentModel.js';
import InventorySkuMovementModel from '../models/inventorySkuMovementModel.js';

export default Marionette.View.extend({
  initialize(options){
    this.listenTo(Radio.channel('inventory'), 'change:bin:sku:count', this.refreshList);
  },
  template: viewTpl,
  regions: {
    list: '[data-region="list"]'
  },
  ui: {
    'addButton': 'button[name="add"]',
  },
  events: {
    'click @ui.addButton': 'add'
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'button:click': 'buttonClicked',
  },
  onRender(){
    this.showList();
  },
  showList(){
    let binSkuCountCollection = Radio.channel('data').request('collection', BinSkuCountCollection, {doFetch: false});
    this.listView = new SearchableListLayoutView({
      collection: binSkuCountCollection,
      listLength: 20,
      searchPath: ['bin.name','sku.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: binSkuCountListTableLayoutTpl,
      entityRowTpl: binSkuCountRowTpl,
      colspan: 6,

    });
    this.showChildView('list', this.listView);
    Radio.channel('app').trigger('navigate', binSkuCountCollection.url(), {trigger: false});
  },
  refreshList(){
    this.listView.search();
  },
  selectModel(childView, args){

  },
  buttonClicked(childView, args){
    let action = args.button.getAttribute('name');
    this[action](args.model);
  },
  add(event){
    event.preventDefault();
    var options = {
      title: 'Add Inventory',
      width: '400px'
    };
    let inventorySkuAdjustment = new InventorySkuAdjustmentModel();
    let view = new InventorySkuAdjustmentActionView({
      model: inventorySkuAdjustment
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  adjust(model){
    var options = {
      title: 'Adjust Inventory',
      width: '400px'
    };
    let inventorySkuAdjustment = new InventorySkuAdjustmentModel({
      forBin: model.get('bin'),
      sku: model.get('sku'),
      oldCount: model.get('count')
    });
    let view = new InventorySkuAdjustmentActionView({
      model: inventorySkuAdjustment
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  moveIn(model){
    var options = {
      title: 'Move In Inventory',
      width: '400px'
    };
    let inventorySkuMovement = new InventorySkuMovementModel({
      toBin: model.get('bin'),
      sku: model.get('sku'),
    });
    let view = new InventorySkuMovementActionView({
      model: inventorySkuMovement
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  moveOut(model){
     var options = {
      title: 'Move Out Inventory',
      width: '400px'
    };
    let inventorySkuMovement = new InventorySkuMovementModel({
      fromBin: model.get('bin'),
      sku: model.get('sku'),
    });
    let view = new InventorySkuMovementActionView({
      model: inventorySkuMovement
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  }
});