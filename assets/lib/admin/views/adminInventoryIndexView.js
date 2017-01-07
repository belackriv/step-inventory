"use strict";

import Marionette from 'marionette';
import viewTpl from  "./adminInventoryIndexView.hbs!";
import Radio from 'backbone.radio';
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './adminInventoryIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#admin-inventory-content",
    tabs: "#admin-inventory-tabs"
  },
  ui: {
    tabLink: "#admin-inventory-tabs a"
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
