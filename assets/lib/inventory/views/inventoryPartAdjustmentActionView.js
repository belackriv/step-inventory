'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import addTpl from './inventoryPartAdjustmentAddActionView.hbs!';
import adjTpl from './inventoryPartAdjustmentActionView.hbs!';

import BinCollection from '../models/binCollection.js';
import PartCollection from '../models/partCollection.js';

export default Marionette.View.extend({
  behaviors: {
    'RemoteSearchSelect2': {
      forBin:{
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
    if(this.model.get('oldCount') === null){
      return addTpl;
    }else{
      return adjTpl;
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
    if(this.model.get('oldCount') === null){
      attr = {
        forBin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.forBin)}),
        part: PartCollection.prototype.model.findOrCreate({id: parseInt(attr.part)}),
        newCount: parseInt(attr.newCount)
      };
    }else{
      attr = {
        newCount: parseInt(attr.newCount)
      };
    }
    return attr;
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
});