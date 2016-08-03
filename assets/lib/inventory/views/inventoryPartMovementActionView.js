'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import toTpl from './inventoryPartMovementToActionView.hbs!';
import fromTpl from './inventoryPartMovementFromActionView.hbs!';

import BinCollection from '../models/binCollection.js';
import PartCollection from '../models/partCollection.js';

export default Marionette.View.extend({
  behaviors: {
    'RemoteSearchSelect2': {
      fromBin:{
        url: BinCollection.prototype.url(),
        search: 'name'
      },
      toBin:{
        url: BinCollection.prototype.url(),
        search: 'name'
      },
      part:{
        url: PartCollection.prototype.url(),
        search: 'name'
      }
    },
  },
  getTemplate(){
    if(this.model.get('toBin') === null){
      return fromTpl;
    }else{
      return toTpl;
    }
  },
  ui: {
    'form': 'form',
    'cancelButton': 'button[data-ui-name="cancel"]',
  },
  events: {
    'submit @ui.form ': 'save',
    'click @ui.cancelButton': 'cancel',
  },
  cancel(){
    this.model.destroy();
    Radio.channel('dialog').trigger('close');
  },
  save(event){
    event.preventDefault();
    this.disableButtons();
    this.model.save(this.getNewAttrs()).done(()=>{
      Radio.channel('dialog').trigger('close');
      Radio.channel('inventory').trigger('change:bin:part:count');
    });
  },
  getNewAttrs(){
    let attr = Syphon.serialize(this);
    if(this.model.get('toBin') === null){
      attr = {
        toBin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.toBin)}),
        count: parseInt(attr.count)
      };
    }else{
      attr = {
        fromBin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.fromBin)}),
        count: parseInt(attr.count)
      };
    }
    return attr;
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
});