'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventorySkuTransformActionView.hbs!';

import SkuCollection from '../models/skuCollection.js';
import InventorySkuTransformModel from '../models/inventorySkuTransformModel.js';
import SalesItemModel from '../models/salesItemModel.js';

export default Marionette.View.extend({
  behaviors: {
    'RemoteSearchSelect2': {
      sku:{
        url: SkuCollection.prototype.selectOptionsUrl,
        search: 'name'
      }
    },
  },
  template: viewTpl,
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
    }).fail(()=>{

    });
  },
  getNewAttrs(){
    let attr = Syphon.serialize(this);
    let sku = SkuCollection.prototype.model.findOrCreate({id: parseInt(attr.sku)});
    let salesItem = SalesItemModel.findOrCreate({
        bin: this.model.get('fromBinSkuCount').get('bin'),
        sku: sku,
        quantity: parseInt(attr.quantity)
    });
    attr = {
      toSalesItem: salesItem,
      quantity: parseInt(attr.quantity)
    };
    return attr;
  },
  disableButtons(){
    this.$el.find('button').addClass('is-loading').prop('disabled', true);
  },
});