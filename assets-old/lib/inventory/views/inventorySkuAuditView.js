'use strict';

import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventorySkuAuditView.hbs!';

import SkuCollection from '../models/skuCollection.js';

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
    Radio.channel('dialog').trigger('close');
  },
  save(event){
    event.preventDefault();
    this.disableButtons();
    this.model.save(this.getNewAttrs()).fail(()=>{
      if(this.model.isNew()){
        this.model.destroy();
      }
    }).done(()=>{
      Radio.channel('dialog').trigger('close');
    });
  },
  getNewAttrs(){
    let attr = Syphon.serialize(this);
    return {
        sku: SkuCollection.prototype.model.findOrCreate({id: parseInt(attr.sku)}),
        userCount: parseInt(attr.userCount)
      };
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
});