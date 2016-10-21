'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryTravelerIdAddActionView.hbs!';

import InboundOrderCollection from 'lib/accounting/models/inboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import SkuCollection from '../models/skuCollection.js';
import TravelerIdModel from '../models/travelerIdModel.js';

export default Marionette.View.extend({
  behaviors: {
    'Stickit': {},
    'RemoteSearchSelect2': {
      inboundOrder:{
        url: InboundOrderCollection.prototype.selectOptionsUrl,
        search: 'label',
        textProperty: 'label',
        placeholder: 'Select Inbound Order'
      },
      bin:{
        url: BinCollection.prototype.selectOptionsUrl,
        search: 'name',
        placeholder: 'Select Bin'
      },
      sku:{
        url: SkuCollection.prototype.selectOptionsUrl,
        search: 'name',
        placeholder: 'Select SKU'
      }
    },
  },
  template: viewTpl,
  ui: {
    'form': 'form',
    'submitButton': 'button[data-ui-name="save"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'countInput': 'input[name="count"]',
    'serialsInput': 'textarea[name="serials"]',
    'serialCount': '[data-ui="serialCount"]',
  },
  bindings: {
    '@ui.countInput': 'count',
    '@ui.serialsInput': 'serials',
  },
  events: {
    'submit @ui.form ': 'save',
    'click @ui.cancelButton': 'cancel',
  },
  modelEvents: {
    'change:serials': 'serialsChanged'
  },
  cancel(){
    this.model.destroy();
    Radio.channel('dialog').trigger('close');
  },
  save(event){
    event.preventDefault();
    this.disableButtons();
    setTimeout(()=>{
      this.addTravelerIds().then(()=>{
        this.model.save().done(()=>{
          Radio.channel('dialog').trigger('close');
          Radio.channel('inventory').trigger('refresh:list:travelerId');
        });
      });
    }, 5);
  },
  serialsChanged(){
    let serialsArray = [];
    _.each(this.ui.serialsInput.val().split('\n'), (serial)=>{
      let trimmedSerial = serial.trim();
      if(trimmedSerial){
        serialsArray.push(trimmedSerial);
      }
    });
    this.model.set('serialsArray', serialsArray);
    this.ui.serialCount.text(serialsArray.length);
  },
  addTravelerIds(){
    return new Promise((resolve, reject)=>{
      let attr = Syphon.serialize(this);
      this.serialsChanged();
      attr = {
        inboundOrder: InboundOrderCollection.prototype.model.findOrCreate({id: parseInt(attr.inboundOrder)}),
        bin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.bin)}),
        sku: SkuCollection.prototype.model.findOrCreate({id: parseInt(attr.sku)}),
        count:  parseInt(attr.count)
      };
      for(var i = 0; i < attr.count; i++){
          let travelerId = new TravelerIdModel({
            inboundOrder: attr.inboundOrder,
            bin: attr.bin,
            sku: attr.sku,
          });
          let serial = this.model.get('serialsArray')[i];
          if(serial){
            travelerId.set('serial', serial);
          }
          this.model.get('travelerIds').add(travelerId);
      }
      resolve();
    });
  },
  disableButtons(){
    this.ui.submitButton.prop('disabled', true).addClass('is-loading');
    this.ui.cancelButton.prop('disabled', true);
  },
  enableButtons(){
    this.ui.submitButton.prop('disabled', false).removeClass('is-loading');
    this.ui.cancelButton.prop('disabled', false);
  },
});