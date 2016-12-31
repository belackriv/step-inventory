"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./salesItemEditView.hbs!";

import OutboundOrderCollection from 'lib/accounting/models/outboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';

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
      outboundOrder:{
        url: OutboundOrderCollection.prototype.url(),
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
    'isVoidInput': 'input[name="isVoid"]',
    'revenueInput': 'input[name="revenue"]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  bindings: {
    '@ui.labelInput': 'label',
    '@ui.isVoidInput': 'isVoid',
    '@ui.revenueInput': 'revenue',
  },
  save(event){
    this.disableFormButtons();
    this.updateSalesItem();
    this.model.save().always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      this.triggerMethod('show:list', this, {
        view: this,
        model:this.model,
      });
    });
  },
  updateSalesItem(){
    let attr = Syphon.serialize(this);
    let setAttr = {
      outboundOrder: OutboundOrderCollection,
      bin: BinCollection,
    };
    _.each(setAttr, (Collection, attributeName)=>{
      if(parseInt(attr[attributeName])){
        let model = Collection.prototype.model.findOrCreate({id: parseInt(attr[attributeName])});
        this.setShownAttributeFromRemoteSelect2(model, attributeName)
        setAttr[attributeName] = model;
      }else{
        delete setAttr[attributeName];
      }
    });
    this.model.set(setAttr);
  },
  setShownAttributeFromRemoteSelect2(model, attributeName){
    if(this.behaviors && this.behaviors.RemoteSearchSelect2 && this.behaviors.RemoteSearchSelect2[attributeName]){
      let options = this.behaviors.RemoteSearchSelect2[attributeName];
      let shownPropertyName = options.textProperty?options.textProperty:options.search;
      //model.set(shownPropertyName, '???');
      let test;
    }
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
