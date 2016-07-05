"use strict";

import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import menuItemTpl from './menuItemView.hbs!';

export default Marionette.View.extend({
  template: menuItemTpl,
  tagName: "li",
  events: {
    "click a": "navigate"
  },
  navigate: function(e){
    e.preventDefault();
    var menuLink = this.model.get('menuLink');
    Radio.channel('app').trigger('navigate', menuLink.url);
  },
});
