"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './navLayoutView.hbs!';
import MenuSelectionView from './menuSelectionView.js';
import LoadingView from 'lib/common/views/loadingView.js';
import OfficeCollection from 'lib/common/models/officeCollection.js';

export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'loading:show', this._showLoading);
    this.listenTo(Radio.channel('app'), 'loading:hide', this._hideLoading);
  },
  template: viewTpl,
  tagName: 'nav',
  className: 'nav has-shadow stepthrough-nav',
  regions: {
    menuSelection: "#menu-selection-container",
    menu: "#nav-menu",
    loading: "#loading-icon-container",
  },
  onRender(){
    let menuSelectionView = new MenuSelectionView({
      model: new Backbone.Model()
    });
    this.showChildView('menuSelection', menuSelectionView);
  },
  _showLoading(){
    this.showChildView('loading', new LoadingView());
  },
  _hideLoading(){
    this.getRegion('loading').empty();
  }
});