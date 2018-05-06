"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './navLayoutView.hbs!';
import NavLeftView from './navLeftView.js';
import NavRightView from './navRightView.js';


export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('data').request('myself'), 'change:organization', this.render);
  },
  template: viewTpl,
  tagName: 'nav',
  className: 'nav has-shadow si-nav',
  regions: {
    navLeft: {
      el: ".nav-left",
      replaceElement: true
    },
    navRight: {
      el: ".nav-right",
      replaceElement: true
    }
  },
  modelEvents: {
    'change:organization': 'render'
  },
  onRender(){
    this.showChildView('navLeft', new NavLeftView());
    this.showChildView('navRight', new NavRightView({
      model: this.model
    }));
  },
});