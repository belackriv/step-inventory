"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminSkusEditView.hbs!";
import PartCollection from 'lib/inventory/models/partCollection.js';
import CommodityCollection from 'lib/inventory/models/commodityCollection.js';
import UnitTypeCollection from 'lib/inventory/models/unitTypeCollection.js';


export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {
      save: 'save'
    },
    'RemoteSearchSelect2': {
      part:{
        url: PartCollection.prototype.selectOptionsUrl,
        search: 'name',
        allowClear: true,
        placeholder: 'Select Part'
      },
      commodity:{
        url: CommodityCollection.prototype.selectOptionsUrl,
        search: 'name',
        allowClear: true,
        placeholder: 'Select Commodity'
      },
      unitType:{
        url: UnitTypeCollection.prototype.selectOptionsUrl,
        search: 'name',
        allowClear: true,
        placeholder: 'Select Unit Type'
      }
    },
  },
  ui: {
    'nameInput': 'input[name="name"]',
    'numberInput': 'input[name="number"]',
    'labelInput': 'input[name="label"]',
    'isVoidInput': 'input[name="isVoid"]',
    'quantityInput': 'input[name="quantity"]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.numberInput': 'number',
    '@ui.labelInput': 'label',
    '@ui.isVoidInput': 'isVoid',
    '@ui.quantityInput': 'quantity',
  },
  save(event){
    this.disableFormButtons();
    this.updateSku();
    this.model.save().always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      this.triggerMethod('show:list', this, {
        view: this,
        model:this.model,
      });
    });
  },
  updateSku(){
    let attr = Syphon.serialize(this);
    let setAttr = {
      part: PartCollection,
      commodity: CommodityCollection,
      unitType: UnitTypeCollection
    };
    _.each(setAttr, (Collection, attributeName)=>{
      if(parseInt(attr[attributeName])){
        setAttr[attributeName] = Collection.prototype.model.findOrCreate({id: parseInt(attr[attributeName])});
      }else{
        setAttr[attributeName] = null;
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
