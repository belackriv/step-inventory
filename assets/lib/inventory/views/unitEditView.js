"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./travelerIdEditView.hbs!";

import InboundOrderCollection from 'lib/accounting/models/inboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import SkuCollection from '../models/skuCollection.js';
import UnitPropertiesListView from './unitTypePropertyValidValuesListView.js';


export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'editUnitButton': 'button[data-ui-name=editUnit]',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  regions: {
    properties: {
      el: '[data-region="properties"]',
      replaceElement: true
    },
  },
  events: {
    'click @ui.editUnitButton': 'editUnit',
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
    this.disableFormButtons();
    let attrs = Syphon.serialize(this);
    this.model.save(attrs).always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      Radio.channel('dialog').trigger('close');
    });
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
