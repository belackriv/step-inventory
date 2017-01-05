"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './navLayoutView.hbs!';
import MenuSelectionView from './menuSelectionView.js';
import UserInfoView from './userInfoView.js';


export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('data').request('myself'), 'change:organization', this.render);
  },
  template: viewTpl,
  tagName: 'nav',
  className: 'nav has-shadow si-nav',
  regions: {
    menuSelection: {
      el: "#menu-selection-container",
      replaceElement: true
    },
    menu: {
      el: "#nav-menu",
      replaceElement: true
    },
    userInfo: {
      el: "#user-info-container",
      replaceElement: true
    }
  },
  modelEvents: {
    'change:organization': 'render'
  },
  onRender(){
    let menuSelectionView = new MenuSelectionView({
      model: new Backbone.Model()
    });
    this.showChildView('menuSelection', menuSelectionView);
    let userInfoView = new UserInfoView({
      model: this.model
    });
    this.showChildView('userInfo', userInfoView);
  },
});