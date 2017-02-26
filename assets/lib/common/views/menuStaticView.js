"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './menuStaticView.hbs!';
import MenuListView from './menuListView.js';

import MenuLinkCollection from '../models/menuLinkCollection.js';
import MenuItemModel from '../models/menuItemModel.js';

export default Marionette.View.extend({
  initialize(){
    this.fullCollection = Radio.channel('data').request('collection', MenuLinkCollection, {fetchAll: true});
    this.collection = new Backbone.Collection();
    this.listenTo(this.fullCollection, 'update', this.updateLinks);
    this.listenTo(Radio.channel('data').request('myself'), 'change:userRoles change:roleHierarchy', this.updateLinks);
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
      collection: this.collection,
    });
    this.showChildView('menuList', menuListView);
    this.updateLinks();
  },
  closeSideMenu(){
    Radio.channel('sideMenu').trigger('toggle');
  },
  updateLinks(){
    this.collection.reset(this.buildMenuItemTree(this.menuItemList));
  },
  doIncludeMenuLink(menuLinkName){
    let myself = Radio.channel('data').request('myself');
    if(myself.isGrantedRole(this.menuLinkRoles[menuLinkName])){
      return true
    }else{
      return false;
    }
  },
  buildMenuItemTree(def){
    let models = [];
    _.each(def, (linkProps)=>{
      if(this.doIncludeMenuLink(linkProps.linkName)){
        let menuLink = this.fullCollection.findWhere({'name': linkProps.linkName});
        let children = this.buildMenuItemTree(linkProps.children);
        if(menuLink){
          models.push(MenuItemModel.findOrCreate({
            menuLink: menuLink,
            children: children,
          }));
        }
      }
    });
    return models;
  },
  menuLinkRoles:{
    'Main': 'ROLE_USER',
    'Inventory Actions': 'ROLE_USER',
    'Inventory Logs': 'ROLE_USER',
    'Inventory Audit': 'ROLE_LEAD',
    'Admin Options': 'ROLE_ADMIN',
    'Admin Inventory': 'ROLE_ADMIN',
    'Admin Accounting': 'ROLE_ADMIN',
    'Reporting': 'ROLE_USER'
  },
  menuItemList:[
    {
      linkName: 'Main',
      children: [ {linkName: 'Inventory Actions'}, { linkName: 'Inventory Logs'}, { linkName: 'Inventory Audit'}]
    },
    {
      linkName: 'Admin Options',
      children: [ {linkName: 'Admin Inventory'}, { linkName: 'Admin Accounting'}]
    },
    {
      linkName: 'Reporting',
      children: []
    }
  ]
});