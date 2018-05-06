"use strict";

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import menuItemTpl from './menuItemView.hbs!';
import MenuItemListView from './menuItemListView.js';

let View = Marionette.View.extend({
  template: menuItemTpl,
  tagName: "li",
  events: {
    "click a": "navigate"
  },
  modelEvents:{
    'change': 'render'
  },
  regions: {
    menuItemChildren: {
      el: 'ul',
      replaceElement: true
    },
  },
  onRender(){
    let menuItemListView = new MenuItemListView({
      collection: this.model.get('children'),
    });
    this.showChildView('menuItemChildren', menuItemListView);
  },
  navigate: function(e){
    e.preventDefault();
    e.stopPropagation();
    Radio.channel('app').trigger('navigate', e.target.getAttribute('href'));
  },
});

globalNamespace.Views.MenuItemView = View;

export default View;
