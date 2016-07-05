"use strict";

import Marionette from 'marionette';
import viewTpl from  "./adminIndexView.hbs!";
import Radio from 'backbone.radio';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
    content: "#admin-content"
  },
  ui: {
    tabLink: "#admin-tabs a"
  },
  events: {
    "click @ui.tabLink": "navigate"
  },
  navigate: function(e){
    e.preventDefault();
    Radio.channel('app').trigger('navigate', $(e.target).attr('href'));
  },
  setActive(){

  }
});
