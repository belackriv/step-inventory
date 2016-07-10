"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './userInfoView.hbs!';
import LoadingView from 'lib/common/views/loadingView.js';

export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'loading:show', this._showLoading);
    this.listenTo(Radio.channel('app'), 'loading:hide', this._hideLoading);
    this.currentTimeUpdateInterval = setInterval(this.model.updateCurrentTime.bind(this.model), 60000);
  },
  template: viewTpl,
  className: 'card is-fullwidth',
  regions: {
    loading: "#loading-icon-container",
  },
  modelEvents:{
    'change': 'render'
  },
   _showLoading(){
    this.showChildView('loading', new LoadingView());
  },
  _hideLoading(){
    this.getRegion('loading').empty();
  },
  onDestroy(){
    clearInterval(this.currentTimeUpdateInterval);
  }
});