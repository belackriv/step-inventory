'use strict';

import _ from 'underscore';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryTravelerIdMoveActionView.hbs!';

import BinCollection from '../models/binCollection.js';

export default Marionette.View.extend({
  behaviors: {
    'RemoteSearchSelect2': {
      bin:{
        url: BinCollection.prototype.url(),
        search: 'name'
      },
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
    this.model.save(this.getAttrs()).done(()=>{
      Radio.channel('dialog').trigger('close');
      //Radio.channel('inventory').trigger('change:bin:part:count');
    });
  },
  getAttrs(){
    let attr = Syphon.serialize(this);
    attr = {
      bin: BinCollection.prototype.model.findOrCreate({id: parseInt(attr.bin)}),
    };
    return attr;
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
});