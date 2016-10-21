'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryAuditView.hbs!';
import InventoryTravelerIdAuditListView from './inventoryTravelerIdAuditListView.js';
import InventorySkuAuditListView from './inventorySkuAuditListView.js';
import InventorySkuAuditView from './inventorySkuAuditView.js';

import InventoryTravelerIdAuditModel from '../models/inventoryTravelerIdAuditModel.js';
import InventorySkuAuditModel from '../models/inventorySkuAuditModel.js';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    travelerIdAudits: {
      el: 'table[data-ui="travelerIdListTable"] > tbody',
      replaceElement: true
    },
    skuAudits: {
      el: 'table[data-ui="skuListTable"] > tbody',
      replaceElement: true
    }
  },
  ui: {
    'travelerIdLabelInput': 'input[name="travelerIdLabel"]',
    'addTravelerIdAuditButton': 'button[name="addTravelerId"]',
    'travelerIdLabelForm': 'form[data-ui="travelerIdLabelForm"]',
    'addSkuAuditButton': 'button[name="addSkuAudit"]',
    'endButton': 'button[name="end"]',
  },
  events: {
    'click @ui.addTravelerIdAuditButton': 'addTravelerId',
    'submit @ui.travelerIdLabelForm': 'addTravelerId',
    'click @ui.addSkuAuditButton': 'addSkuAudit',
    'click @ui.endButton': 'end',
  },
  onRender(){
    Radio.channel('app').trigger('navigate', this.model.url(), {trigger:false});
    this.showTravelerIdAuditList();
    this.showSkuAuditList();
  },
  showTravelerIdAuditList(){
    let travelerIdAuditListView = new InventoryTravelerIdAuditListView({
      collection: this.model.get('inventoryTravelerIdAudits')
    });
    this.showChildView('travelerIdAudits', travelerIdAuditListView);
  },
  showSkuAuditList(){
    let skuAuditListView = new InventorySkuAuditListView({
      collection: this.model.get('inventorySkuAudits')
    });
    this.showChildView('skuAudits', skuAuditListView);
  },
  addTravelerId(event){
    event.preventDefault();
    let travelerIdLabel = this.ui.travelerIdLabelInput.val();
    this.ui.travelerIdLabelInput.focus().val('');
    let travelerId = new InventoryTravelerIdAuditModel({
      inventoryAudit: this.model,
      travelerIdLabel: travelerIdLabel
    }).save();
  },
  addSkuAudit(event){
    event.preventDefault();
    var options = {
      title: 'Add Sku Count',
      width: '400px'
    };
    let inventorySkuAudit = new InventorySkuAuditModel({
      inventoryAudit: this.model
    });
    let view = new InventorySkuAuditView({
      model: inventorySkuAudit
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  end(event){
    event.preventDefault();
    this.disableButtons();
    this.model.save({endedAt: new Date()}).always(()=>{
      this.enableButtons();
    }).done(()=>{
      Radio.channel('app').trigger('navigate', '/inventory_audit');
    });
  },
  disableButtons(){
    this.$el.find('button').prop('disabled', true);
  },
  enableButtons(){
    this.$el.find('button').prop('disabled', false);
  },
});