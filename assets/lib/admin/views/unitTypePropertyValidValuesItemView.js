"use strict";

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './unitTypePropertyValidValuesItemView.hbs!';


let View = Marionette.View.extend({
  template: viewTpl,
  tagName: "li",
  behaviors: {
    'Stickit': {},
  },
  ui: {
    integerValueInput: '[data-value-name="integerValue"]',
    floatValueInput: '[data-value-name="floatValue"]',
    booleanValueInput: '[data-value-name="booleanValue"]',
    stringValueInput: '[data-value-name="stringValue"]',
    removeButton: '[data-ui-name="removeValidValue"]'
  },
  events: {
    'click @ui.removeButton': 'remove'
  },
  modelEvents: {
    'change': 'render',
  },
  bindings: {
    '@ui.integerValueInput': 'integerValue',
    '@ui.floatValueInput': 'floatValue',
    '@ui.booleanValueInput': 'booleanValue',
    '@ui.stringValueInput': 'stringValue',
  },
  remove(){
    this.model.destroy();
  }
});

globalNamespace.Views.UnitTypePropertyValidValuesItemView = View;

export default View;
