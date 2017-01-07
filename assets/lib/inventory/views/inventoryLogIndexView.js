"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from  "./inventoryLogIndexView.hbs!";
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './inventoryLogIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#inventory-content",
    tabs: "#inventory-log-tabs"
  },
  ui: {
    tabLink: "#inventory-log-tabs a"
  },
  events: {
    "click @ui.tabLink": "navigate"
  },
  navigate: function(e){
    e.preventDefault();
    if(e.target.getAttribute('href')){
      Radio.channel('app').trigger('navigate', e.target.getAttribute('href'));
    }
  },
  onRender(){
    this.showChildView('tabs', new NavTabsView({
      template: tabsTpl,
      hasDropdown: true
    }));
  }
});
