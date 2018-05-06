"use strict";

import jquery from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';

import viewTpl from  "./editUnitTypePropertyView.hbs!";
import UnitTypePropertyValidValueModel from 'lib/inventory/models/unitTypePropertyValidValueModel.js';
import UnitTypePropertyValidValuesListView from './unitTypePropertyValidValuesListView.js';

export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'propertyTypeSelect':'select[name="propertyType"]',
    'isRequiredCheckbox':'input[name="isRequired"]',
    'addValidValueButton':'button[data-ui-name="addValidValue"]',
    'saveButton': 'button[data-ui-name="save"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'form': 'form',
  },
  regions: {
    validValues: {
      el: '[data-region="valid-values"]',
      replaceElement: true
    },
  },
  events: {
    'change @ui.propertyTypeSelect': 'propertyTypeChanged',
    'click @ui.addValidValueButton': 'addValidValue',
    'submit @ui.form': 'save',
    'click @ui.cancelButton': 'cancel',
  },
  onRender(){
    let listView = new UnitTypePropertyValidValuesListView({
      collection: this.model.get('validValues')
    });
    this.showChildView('validValues', listView);
  },
  propertyTypeChanged(event){
    event.preventDefault();
    let newPropertyType = this.ui.propertyTypeSelect.val();
    let oldPropertyType = this.model.get('propertyType');
    this.model.set('propertyType', newPropertyType);
    this.model.get('validValues').each((validValue)=>{
      let attrs = {
        [newPropertyType+'Value']: validValue.get(oldPropertyType+'Value'),
        [oldPropertyType+'Value']: null
      };
      validValue.set(attrs);
    });
  },
  addValidValue(event){
    event.preventDefault();
    let validValue = UnitTypePropertyValidValueModel.findOrCreate({
      unitTypeProperty: this.model
    });
  },
  save(event){
    event.preventDefault();
    this.disableFormButtons();
    let attrs = Syphon.serialize(this);
    attrs.isRequired = this.ui.isRequiredCheckbox.prop('checked');
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
