"use strict";

import Marionette from 'marionette';

import viewTpl from './menuLayoutView.hbs!';
import MenuListView from './menuListView.js';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'menu',
  className: 'column menu si-side-menu is-narrow',
  regions: {
    menuList: {
      el: 'ul',
      replaceElement: true
    },
  },
  onRender(){
    let menuListView = new MenuListView({
      collection: this.collection,
    });
    this.showChildView('menuList', menuListView);
  }
});