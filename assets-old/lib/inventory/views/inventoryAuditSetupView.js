'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryAuditSetupView.hbs!';

import BinCollection from '../models/binCollection.js';

export default Marionette.View.extend({
  behaviors: {
    'RemoteSearchSelect2': {
      forBin:{
        url: BinCollection.prototype.url(),
        search: 'name'
      }
    }
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
      Radio.channel('inventory').trigger('resume:audit', this.model);
    });
  },
  getNewAttrs(){
    let attr = Syphon.serialize(this);
    return {
      forBin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.forBin)})
    };
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
});