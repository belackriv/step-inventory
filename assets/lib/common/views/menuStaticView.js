"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './menuStaticView.hbs!';
import MenuListView from './menuListView.js';




export default Marionette.View.extend({
  initialize(){
    this.myself = Radio.channel('data').request('myself');
  },
  template: viewTpl,
  tagName: 'menu',
  className(){
    if(this.options.isMobile){
      return 'mobile-menu si-side-menu is-narrow is-hidden-desktop';
    }else{
      return 'column menu si-side-menu is-narrow is-hidden-touch';
    }
  },
  ui:{
    'closeSideMenu': '[data-ui="closeSideMenu"]',
  },
  regions: {
    menuList: {
      el: 'ul.menu-list',
      replaceElement: true
    },
  },
  events:{
    'click @ui.closeSideMenu': 'closeSideMenu'
  },
  onRender(){
    let menuListView = new MenuListView({
      collection: this.myself.get('menuItems'),
    });
    this.showChildView('menuList', menuListView);
  },
  closeSideMenu(){
    Radio.channel('sideMenu').trigger('toggle');
  },
});