"use strict";

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './orderManifestItemView.hbs!';


let View = Marionette.View.extend({
  template: viewTpl,
  tagName: "tr",
  modelEvents: {
    'change': 'render'
  },
  serializeData: function(){
    var data = _.clone(this.model.attributes);
    data.entityUrl = this.model.url();
    return data;
  },
});

globalNamespace.Views.OrderManifestItemItemView = View;

export default View;
