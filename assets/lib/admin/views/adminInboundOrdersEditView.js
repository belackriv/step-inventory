"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminInboundOrdersEditView.hbs!";
import ClientCollection from 'lib/accounting/models/clientCollection.js';
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
    'clientSelect': 'select[name="client"]',
    'showManifestButton': 'button[data-ui-name="showManifest"]',
  },
  events: {
    'click @ui.showManifestButton': 'showManifest',
  },
  bindings: {
    '@ui.descriptionInput': 'description',
    '@ui.isVoidInput': 'isVoid',
    '@ui.clientSelect': {
      observe: 'client',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', ClientCollection, {fetchAll: true});
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
      title: 'InboundOrder '+this.model.get('label')+' Manifest',
      width: '800px'
    };
    let view = new OrderManifestListTableLayoutView({
      collection: this.model.get('travelerIds'),
      model: this.model,
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', new LoadingView(), options);
    this.model.fetch().then(()=>{
      Radio.channel('dialog').trigger('open', view, options);
    });
  }
});
