"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./travelerIdEditView.hbs!";

import InboundOrderCollection from 'lib/accounting/models/inboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import SkuCollection from '../models/skuCollection.js';
import UnitEditView from './unitEditView.js';


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
      bin:{
        url: BinCollection.prototype.url(),
        search: 'name'
      }
    },
  },
  ui: {
    'labelInput': 'input[name="label"]',
    'serialInput': 'input[name="serial"]',
    'isVoidInput': 'input[name="isVoid"]',
    'quantityInput': 'input[name="quantity"]',
    'costInput': 'input[name="cost"]',
    'inboundOrderSelect' : 'select[name="inboundOrder"]',
    'binSelect' : 'select[name="bin"]',
    'editUnitButton': 'button[data-ui-name=editUnit]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  events: {
    'click @ui.editUnitButton': 'editUnit'
  },
  modelEvents: {
    'change:createdAt': 'render'
  },
  bindings: {
    '@ui.labelInput': 'label',
    '@ui.isVoidInput': 'isVoid',
    '@ui.quantityInput': 'quantity',
    '@ui.costInput': 'cost',
  },
  editUnit(event){
    event.preventDefault();
    let options = {
      title: 'Edit Unit',
      width: '60vw'
    };
    let view = new UnitEditView({
      model: this.model.get('unit'),
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
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
      bin: BinCollection,
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
