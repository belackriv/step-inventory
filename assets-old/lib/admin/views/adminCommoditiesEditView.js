"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminCommoditiesEditView.hbs!";
import UploadImageView from 'lib/common/views/uploadImageView.js';

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
    'commodityIdInput': 'input[name="commodityId"]',
    'commodityAltIdInput': 'input[name="commodityAltId"]',
    'descriptionInput': 'textarea[name="description"]',
    'isActiveInput': 'input[name="isActive"]',
    'partCategorySelect': 'select[name="partCategory"]',
    'partGroupSelect': 'select[name="partGroup"]',
    'uploadButton': 'button[data-ui-name="upload"]',
  },
  events: {
    'click @ui.uploadButton': 'showLogoUploadDialog'
  },
  modelEvents: {
    'change:image': 'render'
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.commodityIdInput': 'commodityId',
    '@ui.commodityAltIdInput': 'commodityAltId',
    '@ui.descriptionInput': 'description',
    '@ui.isActiveInput': 'isActive',
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
  }
});
