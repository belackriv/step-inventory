"use strict";

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./travelerIdView.hbs!";

import TravelerIdActionsView from './travelerIdActionsView.js';
import TravelerIdEditView from './travelerIdEditView.js';
import BinView from './binView.js';
import InboundOrderView from './inboundOrderView.js';
import TravelerIdCardView from './travelerIdCardView.js';

import TravelerIdModel from '../models/travelerIdModel.js';
import InboundOrderModel from 'lib/accounting/models/inboundOrderModel.js';
import BinModel from '../models/binModel.js';

export default Marionette.View.extend({
  initialize(options){
    this.selectedCollection = Radio.channel('data').request('named:collection', Backbone.Collection, 'selectedTids');
    this.listenTo(Radio.channel('inventory'), 'change:isSelected:travelerId', this.isSelectedChanged);
    Radio.channel('inventory').reply('get:isSelected:travelerId', this.getSelectedCollection.bind(this));
  },
  template: viewTpl,
  regions: {
    content: '[data-region="content"]'
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'show:list': 'showList',
    'show:inboundOrder': 'showInboundOrder',
    'show:bin': 'showBin',
    'show:card': 'showCard',
  },
  onRender(){
    if(this.options.id){
      let tidModel = TravelerIdModel.findOrCreate({id: parseInt(this.options.id)});
      tidModel.fetch();
      this.selectModel({model: tidModel});
    }else if(this.options.inboundOrder){
      let inboundOrderModel = InboundOrderModel.findOrCreate({id: this.options.inboundOrder});
       this.showInboundOrder(inboundOrderModel);
    }else if(this.options.bin){
      let binModel = BinModel.findOrCreate({id: this.options.bin});
       this.showBin(binModel);
    }else if(this.options.show){
      let tidModel = TravelerIdModel.findOrCreate({id: this.options.show});
      tidModel.fetch();
      this.showCard(tidModel);
    }else{
      this.showList();
    }
  },
  showList(){
    this.showChildView('content', new TravelerIdActionsView());
    Radio.channel('app').trigger('navigate', '/tid', {trigger: false});
  },
  selectModel(args){
    this.showChildView('content', new TravelerIdEditView({
      model: args.model
    }));
    Radio.channel('app').trigger('navigate', args.model.url(), {trigger: false});
  },
  showInboundOrder(inboundOrder){
    this.showChildView('content', new InboundOrderView({
      model: inboundOrder
    }));
    inboundOrder.fetch();
    Radio.channel('app').trigger('navigate', '/show'+inboundOrder.url(), {trigger: false});
  },
  showBin(bin){
    this.showChildView('content', new BinView({
      model: bin
    }));
    bin.fetch();
    Radio.channel('app').trigger('navigate', '/show'+bin.url(), {trigger: false});
  },
  showCard(travelerId){
    this.showChildView('content', new TravelerIdCardView({
      model: travelerId
    }));
    Radio.channel('app').trigger('navigate', '/show'+travelerId.url(), {trigger: false});
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
