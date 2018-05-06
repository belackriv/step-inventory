"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminOrganizationsEditView.hbs!";
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
    'uploadButton': 'button[data-ui-name="upload"]'
  },
  events: {
    'click @ui.uploadButton': 'showLogoUploadDialog'
  },
  modelEvents: {
    'change:logo': 'render'
  },
  bindings: {
    '@ui.nameInput': 'name',
  },
  showLogoUploadDialog(event){
    event.preventDefault();
    let options = {
      title: 'Upload Logo',
      width: '400px'
    };
    let view = new UploadImageView({
      model: this.model,
      imageAttributeName: 'logo',
      organization: this.model
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  }
});
