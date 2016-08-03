'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './binPartCountView.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import binPartCountListTableLayoutTpl from './binPartCountListTableLayoutTpl.hbs!';
import binPartCountRowTpl from './binPartCountRowTpl.hbs!';
import InventoryPartAdjustmentActionView from './inventoryPartAdjustmentActionView.js';
import InventoryPartMovementActionView from './inventoryPartMovementActionView.js';

import BinPartCountCollection from '../models/binPartCountCollection.js';
import InventoryPartAdjustmentModel from '../models/inventoryPartAdjustmentModel.js';
import InventoryPartMovementModel from '../models/inventoryPartMovementModel.js';

export default Marionette.View.extend({
  initialize(options){
    this.listenTo(Radio.channel('inventory'), 'change:bin:part:count', this.refreshList);
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
    let binPartCountCollection = Radio.channel('data').request('collection', BinPartCountCollection, {doFetch: false});
    this.listView = new SearchableListLayoutView({
      collection: binPartCountCollection,
      listLength: 20,
      searchPath: ['bin.name','part.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: binPartCountListTableLayoutTpl,
      entityRowTpl: binPartCountRowTpl,
      colspan: 6,

    });
    this.showChildView('list', this.listView);
    Radio.channel('app').trigger('navigate', binPartCountCollection.url(), {trigger: false});
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
    let inventoryPartAdjustment = new InventoryPartAdjustmentModel();
    let view = new InventoryPartAdjustmentActionView({
      model: inventoryPartAdjustment
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  adjust(model){
    var options = {
      title: 'Adjust Inventory',
      width: '400px'
    };
    let inventoryPartAdjustment = new InventoryPartAdjustmentModel({
      forBin: model.get('bin'),
      part: model.get('part'),
      oldCount: model.get('count')
    });
    let view = new InventoryPartAdjustmentActionView({
      model: inventoryPartAdjustment
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  moveIn(model){
    var options = {
      title: 'Move In Inventory',
      width: '400px'
    };
    let inventoryPartMovement = new InventoryPartMovementModel({
      toBin: model.get('bin'),
      part: model.get('part'),
    });
    let view = new InventoryPartMovementActionView({
      model: inventoryPartMovement
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  moveOut(model){
     var options = {
      title: 'Move Out Inventory',
      width: '400px'
    };
    let inventoryPartMovement = new InventoryPartMovementModel({
      fromBin: model.get('bin'),
      part: model.get('part'),
    });
    let view = new InventoryPartMovementActionView({
      model: inventoryPartMovement
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  }
});