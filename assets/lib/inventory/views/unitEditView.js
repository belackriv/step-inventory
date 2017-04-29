"use strict";

import _ from 'underscore';
import jquery from 'jquery';
import Backbone from 'backbone';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./unitEditView.hbs!";

import InboundOrderCollection from 'lib/accounting/models/inboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import SkuCollection from '../models/skuCollection.js';
import UnitPropertiesListView from './unitPropertiesListView.js';

export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'form': 'form',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
  },
  regions: {
    properties: {
      el: '[data-region="properties"]',
      replaceElement: true
    },
  },
  events: {
    'click @ui.cancelButton': 'cancel',
    'submit @ui.form': 'save',
  },
  onRender(){
    let listView = new UnitPropertiesListView({
      collection: this.model.get('properties')
    });
    this.showChildView('properties', listView);
  },
  save(event){
    event.preventDefault();
    event.stopPropagation();
    this.disableFormButtons();
    let attrs = Syphon.serialize(this);
    let unitModel = this.model;
    this.$el.find('[data-unit-property-id]').each((idx, propertyInput)=>{
      let property = unitModel.get('properties').get(jquery(propertyInput).data('unitPropertyId'));
      property.typeAndSet(jquery(propertyInput).data('valueName'), jquery(propertyInput).val());
    });
    this.model.save(attrs).always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      Radio.channel('dialog').trigger('close');
    });
  },
  cancel(event){
    event.preventDefault();
    Radio.channel('dialog').trigger('close');
  },
  disableFormButtons(){
    this.ui.saveButton.addClass('is-disabled').prop('disable', true);
    this.ui.cancelButton.addClass('is-disabled').prop('disable', true);
  },
  enableFormButtons(){
    this.ui.saveButton.removeClass('is-disabled').prop('disable', false);
    this.ui.cancelButton.removeClass('is-disabled').prop('disable', false);
  },
});
