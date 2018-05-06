"use strict";

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./salesItemView.hbs!";

import SalesItemActionsView from './salesItemActionsView.js';
import SalesItemEditView from './salesItemEditView.js';
import BinView from './binView.js';
import OutboundOrderView from './outboundOrderView.js';
import SalesItemCardView from './salesItemCardView.js';

import SalesItemModel from '../models/salesItemModel.js';
import OutboundOrderModel from 'lib/accounting/models/outboundOrderModel.js';
import BinModel from '../models/binModel.js';

export default Marionette.View.extend({
  initialize(options){
    this.selectedCollection = Radio.channel('data').request('named:collection', Backbone.Collection, 'selectedSalesItems');
    this.listenTo(Radio.channel('inventory'), 'change:isSelected:salesItem', this.isSelectedChanged);
    Radio.channel('inventory').reply('get:isSelected:salesItem', this.getSelectedCollection.bind(this));
  },
  template: viewTpl,
  regions: {
    content: '[data-region="content"]'
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'show:list': 'showList',
    'show:outboundOrder': 'showOutboundOrder',
    'show:bin': 'showBin',
    'show:card': 'showCard',
  },
  onRender(){
    if(this.options.id){
      let salesItemModel = SalesItemModel.findOrCreate({id: parseInt(this.options.id)});
      salesItemModel.fetch();
      this.selectModel({model: salesItemModel});
    }else if(this.options.outboundOrder){
      let outboundOrderModel = OutboundOrderModel.findOrCreate({id: this.options.outboundOrder});
       this.showOutboundOrder(outboundOrderModel);
    }else if(this.options.bin){
      let binModel = BinModel.findOrCreate({id: this.options.bin});
       this.showBin(binModel);
    }else if(this.options.show){
      let salesItemModel = SalesItemModel.findOrCreate({id: this.options.show});
      salesItemModel.fetch();
      this.showCard(salesItemModel);
    }else{
      this.showList();
    }
  },
  showList(){
    this.showChildView('content', new SalesItemActionsView());
    Radio.channel('app').trigger('navigate', '/sales_item', {trigger: false});
  },
  selectModel(args){
    this.showChildView('content', new SalesItemEditView({
      model: args.model
    }));
    Radio.channel('app').trigger('navigate', args.model.url(), {trigger: false});
  },
  showCard(salesItem){
    this.showChildView('content', new SalesItemCardView({
      model: salesItem
    }));
    Radio.channel('app').trigger('navigate', '/show'+salesItem.url(), {trigger: false});
  },
  showOutboundOrder(outboundOrder){
    this.showChildView('content', new OutboundOrderView({
      model: outboundOrder
    }));
    outboundOrder.fetch();
    Radio.channel('app').trigger('navigate', '/show'+outboundOrder.url(), {trigger: false});
  },
  showBin(bin){
    this.showChildView('content', new BinView({
      model: bin
    }));
    bin.fetch();
    Radio.channel('app').trigger('navigate', '/show'+bin.url(), {trigger: false});
  },
  getSelectedCollection(){
    return this.selectedCollection;
  },
  isSelectedChanged(model){
    if(model.get('isSelected')){
      this.selectedCollection.add(model);
    }else{
      this.selectedCollection.remove(model);
    }
  }
});
