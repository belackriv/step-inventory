'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryTravelerIdAddActionView.hbs!';

import InboundOrderCollection from 'lib/accounting/models/inboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import PartCollection from '../models/partCollection.js';
import TravelerIdModel from '../models/travelerIdModel.js';

export default Marionette.View.extend({
  behaviors: {
    'Stickit': {},
    'RemoteSearchSelect2': {
      inboundOrder:{
        url: InboundOrderCollection.prototype.url(),
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
  template: viewTpl,
  ui: {
    'form': 'form',
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
    this.addTravelerIds();
    this.model.save().done(()=>{
      Radio.channel('dialog').trigger('close');
      Radio.channel('inventory').trigger('refresh:list:travelerId');
    });
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
    let attr = Syphon.serialize(this);
    this.serialsChanged();
    attr = {
      inboundOrder: InboundOrderCollection.prototype.model.findOrCreate({id: parseInt(attr.inboundOrder)}),
      bin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.bin)}),
      part: PartCollection.prototype.model.findOrCreate({id: parseInt(attr.part)}),
      count:  parseInt(attr.count)
    };
    for(var i = 0; i < attr.count; i++){
      let travelerId = new TravelerIdModel({
        inboundOrder: attr.inboundOrder,
        bin: attr.bin,
        part: attr.part,
      });
      let serial = this.model.get('serialsArray')[i];
      if(serial){
        travelerId.set('serial', serial);
      }
      this.model.get('travelerIds').add(travelerId);
    }
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
});