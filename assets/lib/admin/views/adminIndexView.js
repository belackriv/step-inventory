"use strict";

import Marionette from 'marionette';
import viewTpl from  "./adminIndexView.hbs!";
import Radio from 'backbone.radio';
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './adminIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#admin-content",
    tabs: "#admin-tabs"
  },
  ui: {
    tabLink: "#admin-tabs a"
  },
  events: {
    "click @ui.tabLink": "navigate"
  },
  navigate: function(e){
    e.preventDefault();
    Radio.channel('app').trigger('navigate', e.target.getAttribute('href'));
  },
  onRender(){
    this.showChildView('tabs', new NavTabsView({
      template: tabsTpl
    }));
  }
});
