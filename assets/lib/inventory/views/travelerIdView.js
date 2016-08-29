'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './travelerIdView.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import travelerIdListTableLayoutTpl from './travelerIdListTableLayoutTpl.hbs!';
import travelerIdRowTpl from './travelerIdRowTpl.hbs!';

import InventoryTravelerIdAddActionView from './inventoryTravelerIdAddActionView.js';
import InventoryTravelerIdMovementActionView from './inventoryTravelerIdMovementActionView.js';

import TravelerIdCollection from '../models/travelerIdCollection.js';
import MassTravelerIdModel from '../models/massTravelerIdModel.js';
import InventoryTravelerIdMovementModel from '../models/inventoryTravelerIdMovementModel.js';

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
    let travelerIdCollection = Radio.channel('data').request('collection', TravelerIdCollection, {doFetch: false});
    this.listView = new SearchableListLayoutView({
      collection: travelerIdCollection,
      listLength: 20,
      searchPath: ['label','serial', 'inboundOrder.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: travelerIdListTableLayoutTpl,
      entityRowTpl: travelerIdRowTpl,
      colspan: 6,

    });
    this.showChildView('list', this.listView);
    Radio.channel('app').trigger('navigate', travelerIdCollection.url(), {trigger: false});
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
    let massTravelerId = new MassTravelerIdModel();
    let view = new InventoryTravelerIdAddActionView({
      model: massTravelerId
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  move(model){
    var options = {
      title: 'Move Inventory',
      width: '400px'
    };
    let inventoryMovement = new InventoryTravelerIdMovementModel({
      fromBin: model.get('bin'),
      part: model.get('part'),
    });
    let view = new InventoryTravelerIdMovementActionView({
      model: inventoryMovement
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  }
});