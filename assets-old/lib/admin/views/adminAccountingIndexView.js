"use strict";

import Marionette from 'marionette';
import viewTpl from  "./adminAccountingIndexView.hbs!";
import Radio from 'backbone.radio';
import NavTabsView from 'lib/common/views/navTabsView.js';
import tabsTpl from './adminAccountingIndexTabsView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#admin-accounting-content",
    tabs: "#admin-accounting-tabs"
  },
  ui: {
    tabLink: "#admin-accounting-tabs a"
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
