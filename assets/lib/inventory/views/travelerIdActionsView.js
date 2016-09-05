'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './travelerIdActionsView.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import travelerIdListTableLayoutTpl from './travelerIdListTableLayoutTpl.hbs!';
import travelerIdRowTpl from './travelerIdRowTpl.hbs!';

import InventoryTravelerIdAddActionView from './inventoryTravelerIdAddActionView.js';
import InventoryTravelerIdMassEditActionView from './inventoryTravelerIdMassEditActionView.js';
import InventoryTravelerIdMassSelectionActionView from './inventoryTravelerIdMassSelectionActionView.js';

import TravelerIdCollection from '../models/travelerIdCollection.js';
import MassTravelerIdModel from '../models/massTravelerIdModel.js';


export default Marionette.View.extend({
  initialize(options){
    this.listenTo(Radio.channel('inventory'), 'refresh:list:travelerId', this.refreshList);
  },
  template: viewTpl,
  regions: {
    list: '[data-region="list"]'
  },
  ui: {
    'addButton': 'button[name="add"]',
    'massEditButton': 'button[name="massEdit"]',
    'massSelectButton': 'button[name="massSelect"]',
  },
  events: {
    'click @ui.addButton': 'add',
    'click @ui.massEditButton': 'massEdit',
    'click @ui.massSelectButton': 'massSelect',
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
      colspan: 7,
    });
    this.showChildView('list', this.listView);
    Radio.channel('app').trigger('navigate', travelerIdCollection.url(), {trigger: false});
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
  selectAllToggleValue: false,
  toggleSelectAll(){
    this.selectAllToggleValue = !this.selectAllToggleValue;
    let collection = this.listView.getCurrentCollection();
    if(collection){
      collection.invoke('set','isSelected', this.selectAllToggleValue);
   }
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
  massEdit(event){
    event.preventDefault();
    var options = {
      title: 'Mass Edit Inventory',
      width: '400px'
    };
    let view = new InventoryTravelerIdMassEditActionView();
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  massSelect(event){
    event.preventDefault();
     var options = {
      title: 'Mass Select Inventory',
      width: '400px'
    };
    let view = new InventoryTravelerIdMassSelectionActionView();
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
});