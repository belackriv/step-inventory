"use strict";

import Marionette from 'marionette';
import viewTpl from  "./reportingIndexView.hbs!";
import Radio from 'backbone.radio';
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './reportingIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#reporting-content",
    tabs: "#reporting-tabs"
  },
  ui: {
    tabLink: "#reporting-tabs a"
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
