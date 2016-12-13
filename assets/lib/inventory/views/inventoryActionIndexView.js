"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from  "./inventoryActionIndexView.hbs!";
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './inventoryActionIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#inventory-content",
    tabs: "#inventory-action-tabs"
  },
  ui: {
    tabLink: "#inventory-action-tabs a"
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
