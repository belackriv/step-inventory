"use strict";

import _ from 'underscore';
import Handlebars from 'handlebars/handlebars.runtime.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './userInfoView.hbs!';
import LoadingView from 'lib/common/views/loadingView.js';

export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'loading:show', this._showLoading);
    this.listenTo(Radio.channel('app'), 'loading:hide', this._hideLoading);
    this.currentTimeUpdateInterval = setInterval(this.model.updateCurrentTime.bind(this.model), 1000);
  },
  template: viewTpl,
  className: 'card is-fullwidth',
  ui:{
    'currentTime': '[data-ui="currentTime"]',
  },
  events:{
    "click a": "navigate"
  },
  regions: {
    loading: "#loading-icon-container",
  },
  modelEvents:{
    'change:firstName': 'render',
    'change:appMessage': 'render',
    'change:currentTime': 'currentTimeChanged'
  },
   _showLoading(){
    this.showChildView('loading', new LoadingView());
  },
  _hideLoading(){
    this.getRegion('loading').empty();
  },
  currentTimeChanged(){
    this.ui.currentTime.text(Handlebars.helpers.moment(this.model.get('currentTime'),{hash: {}}));
  },
 navigate: function(e){
    if(e.currentTarget.dataset.defaultNavAction !== 'true'){
      e.preventDefault();
      e.stopPropagation();
      Radio.channel('app').trigger('navigate', e.currentTarget.getAttribute('href'));
    }
  },
  onDestroy(){
    clearInterval(this.currentTimeUpdateInterval);
  }
});