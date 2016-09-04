"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./travelerIdEditView.hbs!";

import InboundOrderCollection from 'lib/accounting/models/inboundOrderCollection.js';
import OutboundOrderCollection from 'lib/accounting/models/outboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import PartCollection from '../models/partCollection.js';


export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'SaveCancelDelete': {
      save: 'save'
    },
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'RemoteSearchSelect2': {
      inboundOrder:{
        url: InboundOrderCollection.prototype.url(),
        search: 'label',
        textProperty: 'label'
      },
      outboundOrder:{
        url: OutboundOrderCollection.prototype.url(),
        search: 'label',
        textProperty: 'label'
      },
      bin:{
        url: BinCollection.prototype.url(),
        search: 'name'
      },
      part:{
        url: PartCollection.prototype.url(),
        search: 'name'
      }
    },
  },
  ui: {
    'labelInput': 'input[name="label"]',
    'serialInput': 'input[name="serial"]',
    'isVoidInput': 'input[name="isVoid"]',
    'costInput': 'input[name="cost"]',
    'revenueInput': 'input[name="revenue"]',
    'inboundOrderSelect' : 'select[name="inboundOrder"]',
    'outboundOrderSelect' : 'select[name="outboundOrder"]',
    'binSelect' : 'select[name="bin"]',
    'partSelect' : 'select[name="part"]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  bindings: {
    '@ui.labelInput': 'label',
    '@ui.serialInput': 'serial',
    '@ui.isVoidInput': 'isVoid',
    '@ui.costInput': 'cost',
    '@ui.revenueInput': 'revenue',
  },
  save(event){
    this.disableFormButtons();
    this.updateTravelerId();
    this.model.save().always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      this.triggerMethod('show:list', this, {
        view: this,
        model:this.model,
      });
    });
  },
  updateTravelerId(){
    let attr = Syphon.serialize(this);
    let setAttr = {
      inboundOrder: InboundOrderCollection,
      outboundOrder: OutboundOrderCollection,
      bin: BinCollection,
      part: PartCollection,
    };
    _.each(setAttr, (Collection, attributeName)=>{
      if(parseInt(attr[attributeName])){
        setAttr[attributeName] = Collection.prototype.model.findOrCreate({id: parseInt(attr[attributeName])});
      }else{
        delete setAttr[attributeName];
      }
    });
    this.model.set(setAttr);
  },
  disableFormButtons(){
    this.ui.saveButton.addClass('is-disabled').prop('disable', true);
    this.ui.cancelButton.addClass('is-disabled').prop('disable', true);
    this.ui.deleteButton.addClass('is-disabled').prop('disable', true);
  },
  enableFormButtons(){
    this.ui.saveButton.removeClass('is-disabled').prop('disable', false);
    this.ui.cancelButton.removeClass('is-disabled').prop('disable', false);
    this.ui.deleteButton.removeClass('is-disabled').prop('disable', false);
  },
});
