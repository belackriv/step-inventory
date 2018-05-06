"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminUnitTypesEditView.hbs!";
import UploadImageView from 'lib/common/views/uploadImageView.js';
import UnitTypePropertyModel from 'lib/inventory/models/unitTypePropertyModel.js';
import UnitTypePropertiesListView from './unitTypePropertiesListView.js';
import EditUnitTypePropertyView from './editUnitTypePropertyView.js';

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  ui: {
    'nameInput': 'input[name="name"]',
    'manufacturerInput': 'input[name="manufacturer"]',
    'modelInput': 'input[name="model"]',
    'descriptionInput': 'textarea[name="description"]',
    'isActiveInput': 'input[name="isActive"]',
    'partCategorySelect': 'select[name="partCategory"]',
    'partGroupSelect': 'select[name="partGroup"]',
    'uploadButton': 'button[data-ui-name="upload"]',
    'addPropertyButton': 'button[data-ui-name="addProperty"]',
    'removePropertyButton': 'button[data-ui-name="removeProperty"]',
    'propertyLoadingIndicator': 'span[data-ui-name="propertyLoadingIndicator"]'
  },
  regions: {
    properties: {
      el: '[data-region="properties"]',
      replaceElement: true
    },
  },
  events: {
    'click @ui.uploadButton': 'showLogoUploadDialog',
    'click @ui.addPropertyButton': 'addProperty',
    'click @ui.removePropertyButton': 'removeProperty',
  },
  modelEvents: {
    'change:image': 'render'
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.manufacturerInput': 'manufacturer',
    '@ui.modelInput': 'model',
    '@ui.descriptionInput': 'description',
    '@ui.isActiveInput': 'isActive',
  },
  onRender(){
    this.ui.propertyLoadingIndicator.hide();
    let listView = new UnitTypePropertiesListView({
      collection: this.model.get('properties')
    });
    this.showChildView('properties', listView);
  },
  showLogoUploadDialog(event){
    let myself = Radio.channel('data').request('myself');
    let organization = myself.get('organization');
    event.preventDefault();
    let options = {
      title: 'Upload Image',
      width: '400px'
    };
    let view = new UploadImageView({
      model: this.model,
      imageAttributeName: 'image',
      organization: organization
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  addProperty(event){
    event.preventDefault();
    let property = UnitTypePropertyModel.findOrCreate({
      unitType: this.model
    });
    let options = {
      title: 'Add Property',
      width: '600px'
    };
    let view = new EditUnitTypePropertyView({
      model: property,
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },

});
