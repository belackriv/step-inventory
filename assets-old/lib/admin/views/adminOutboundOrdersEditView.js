"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';

import viewTpl from  "./adminOutboundOrdersEditView.hbs!";
import CustomerCollection from 'lib/accounting/models/customerCollection.js';
import LoadingView from 'lib/common/views/loadingView.js';
import OrderManifestListTableLayoutView from './orderManifestListTableLayoutView.js';

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {
      save: 'save'
    },
    'RemoteSearchSelect2': {
      customer:{
        url: CustomerCollection.prototype.url(),
        search: 'name'
      }
    },
  },
  ui: {
    'descriptionInput': 'textarea[name="description"]',
    'isVoidInput': 'input[name="isVoid"]',
    'customerSelect': 'select[name="customer"]',
    'showManifestButton': 'button[data-ui-name="showManifest"]',
    'shipOrderButton': 'button[data-ui-name="shipOrder"]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  bindings: {
    '@ui.descriptionInput': 'description',
    '@ui.isVoidInput': 'isVoid',
  },
  events: {
    'click @ui.showManifestButton': 'showManifest',
    'click @ui.shipOrderButton': 'shipOrder',
  },
  showManifest(event){
    event.preventDefault();
    let options = {
      title: 'OutboundOrder '+this.model.get('label')+' Manifest',
      width: '800px'
    };
    let view = new OrderManifestListTableLayoutView({
      collection: this.model.get('salesItems'),
      model: this.model,
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', new LoadingView(), options);
    this.model.fetch().then(()=>{
      Radio.channel('dialog').trigger('open', view, options);
    });
  },
  shipOrder(event){
    event.preventDefault();
    this.ui.shipOrderButton.prop('disabled', true);
    this.ui.shipOrderButton.addClass('is-disabled');
    this.ui.shipOrderButton.addClass('is-loading');
    this.model.ship().then(()=>{
      this.ui.shipOrderButton.removeClass('is-loading');
    });
  },
  save(event){
    this.disableFormButtons();
    this.update();
    this.model.save().always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      this.triggerMethod('show:list', this, {
        view: this,
        model:this.model,
      });
    });
  },
  update(){
    let attr = Syphon.serialize(this);
    let setAttr = {
      customer: CustomerCollection,
    };
    _.each(setAttr, (Collection, attributeName)=>{
      if(parseInt(attr[attributeName])){
        setAttr[attributeName] = Collection.prototype.model.findOrCreate({id: parseInt(attr[attributeName])});
      }else{
        delete setAttr[attributeName];
      }
    });
    this.model.set(setAttr);
  },
  disableFormButtons(){
    this.ui.saveButton.addClass('is-disabled').prop('disable', true);
    this.ui.cancelButton.addClass('is-disabled').prop('disable', true);
    this.ui.deleteButton.addClass('is-disabled').prop('disable', true);
  },
  enableFormButtons(){
    this.ui.saveButton.removeClass('is-disabled').prop('disable', false);
    this.ui.cancelButton.removeClass('is-disabled').prop('disable', false);
    this.ui.deleteButton.removeClass('is-disabled').prop('disable', false);
  },
});
