"use strict";

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./travelerIdView.hbs!";

import TravelerIdActionsView from './travelerIdActionsView.js';
import TravelerIdEditView from './travelerIdEditView.js';
import BinView from './binView.js';

import TravelerIdModel from '../models/travelerIdModel.js';
import BinModel from '../models/binModel.js';

export default Marionette.View.extend({
  initialize(options){
    this.selectedCollection = new Backbone.Collection();
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
    'show:bin': 'showBin',
  },
  onRender(){
    if(this.options.id){
      let tid = TravelerIdModel.findOrCreate({id: parseInt(this.options.id)});
      tid.fetch();
      this.selectModel({model: tid});
    }else if(this.options.bin){
      let binModel = BinModel.findOrCreate({id: this.options.bin});
       this.showBin(binModel);
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
