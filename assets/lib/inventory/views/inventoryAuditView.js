'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryAuditView.hbs!';
import InventoryPartAuditListView from './inventoryPartAuditListView.js';
import InventoryPartAuditView from './inventoryPartAuditView.js';

import InventoryPartAuditModel from '../models/inventoryPartAuditModel.js';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    partAudits: {
      el: 'table[data-ui="partListTable"] > tbody',
      replaceElement: true
    },
  },
  ui: {
    'addPartAuditButton': 'button[name="addPartAudit"]',
    'endButton': 'button[name="end"]',
  },
  events: {
    'click @ui.addPartAuditButton': 'addPartAudit',
    'click @ui.endButton': 'end',
  },
  onRender(){
    Radio.channel('app').trigger('navigate', this.model.url(), {trigger:false});
    this.showPartAuditList();
  },
  showPartAuditList(){
    let partAuditListView = new InventoryPartAuditListView({
      collection: this.model.get('inventoryPartAudits')
    });
    this.showChildView('partAudits', partAuditListView);
  },
  addPartAudit(event){
    event.preventDefault();
    var options = {
      title: 'Add Part Count',
      width: '400px'
    };
    let inventoryPartAudit = new InventoryPartAuditModel({
      inventoryAudit: this.model
    });
    let view = new InventoryPartAuditView({
      model: inventoryPartAudit
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