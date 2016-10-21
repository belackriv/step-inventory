'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import addTpl from './inventorySkuAdjustmentAddActionView.hbs!';
import adjTpl from './inventorySkuAdjustmentActionView.hbs!';

import BinCollection from '../models/binCollection.js';
import SkuCollection from '../models/skuCollection.js';

export default Marionette.View.extend({
  behaviors: {
    'RemoteSearchSelect2': {
      forBin:{
        url: BinCollection.prototype.selectOptionsUrl,
        search: 'name'
      },
      sku:{
        url: SkuCollection.prototype.selectOptionsUrl,
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
      Radio.channel('inventory').trigger('change:bin:sku:count');
    });
  },
  getNewAttrs(){
    let attr = Syphon.serialize(this);
    if(this.model.get('oldCount') === null){
      attr = {
        forBin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.forBin)}),
        sku: SkuCollection.prototype.model.findOrCreate({id: parseInt(attr.sku)}),
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