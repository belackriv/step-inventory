"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
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
    'SaveCancelDelete': {},
  },
  ui: {
    'descriptionInput': 'textarea[name="description"]',
    'isVoidInput': 'input[name="isVoid"]',
    'customerSelect': 'select[name="customer"]',
    'showManifestButton': 'button[data-ui-name="showManifest"]',
    'shipOrderButton': 'button[data-ui-name="shipOrder"]',
  },
  events: {
    'click @ui.showManifestButton': 'showManifest',
    'click @ui.shipOrderButton': 'shipOrder',
  },
  bindings: {
    '@ui.descriptionInput': 'description',
    '@ui.isVoidInput': 'isVoid',
    '@ui.customerSelect': {
      observe: 'customer',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', CustomerCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
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
  }
});
