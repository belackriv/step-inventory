"use strict";

import Marionette from 'marionette';
import viewTpl from  "./inventoryIndexView.hbs!";
import Radio from 'backbone.radio';
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './inventoryIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#inventory-content",
    tabs: "#inventory-tabs"
  },
  ui: {
    tabLink: "#inventory-tabs a"
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
