"use strict";

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './unitTypePropertiesItemView.hbs!';
import EditUnitTypePropertyView from './editUnitTypePropertyView.js';

let View = Marionette.View.extend({
  template: viewTpl,
  tagName: "li",
  ui: {
    editButton: '[data-ui-name="editProperty"]',
    removeButton: '[data-ui-name="removeProperty"]'
  },
  events: {
    'click @ui.editButton': 'edit',
    'click @ui.removeButton': 'remove'
  },
  modelEvents: {
    'change': 'render'
  },
  edit(event){
    event.preventDefault();
    let options = {
      title: 'Edit Property',
      width: '600px'
    };
    let view = new EditUnitTypePropertyView({
      model: this.model,
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  remove(){
    this.model.destroy();
  }
});

globalNamespace.Views.UnitTypePropertiesItemView = View;

export default View;
