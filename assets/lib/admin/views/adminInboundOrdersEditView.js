"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Syphon from 'backbone.syphon';
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
    'SaveCancelDelete': {
      save: 'save'
    },
    'RemoteSearchSelect2': {
      client:{
        url: ClientCollection.prototype.url(),
        search: 'name'
      }
    },
  },
  ui: {
    'descriptionInput': 'textarea[name="description"]',
    'isVoidInput': 'input[name="isVoid"]',
    'expectedAtInput': 'input[name="expectedAt"]',
    'clientSelect': 'select[name="client"]',
    'showManifestButton': 'button[data-ui-name="showManifest"]',
    'receiveOrderButton': 'button[data-ui-name="receiveOrder"]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  bindings: {
    '@ui.descriptionInput': 'description',
    '@ui.isVoidInput': 'isVoid',
    '@ui.expectedAtInput': 'expectedAt',
  },
  events: {
    'click @ui.showManifestButton': 'showManifest',
    'click @ui.receiveOrderButton': 'receiveOrder',
  },
  onAttach(){
    this.$el.find('input[type="date"]').datetimepicker({
      //format:  'YYYY-MM-DD[T]HH:mm:ss.SSSSSZ',
      format: 'Y-m-d\\TH:i:s.00000P',
      timepicker: false,
      formatDate: 'Y-m-d',
      formatTime: 'H:i',
      showOtherMonths: true,
      selectOtherMonths: true
    }).attr('type','text');
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
  },
  receiveOrder(event){
    event.preventDefault();
    this.ui.receiveOrderButton.prop('disabled', true);
    this.ui.receiveOrderButton.addClass('is-disabled');
    this.ui.receiveOrderButton.addClass('is-loading');
    this.model.receive().then(()=>{
      this.ui.receiveOrderButton.removeClass('is-loading');
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
      client: ClientCollection,
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
