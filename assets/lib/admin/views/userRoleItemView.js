'use strict';

import Marionette from 'marionette';
import viewTpl from  "./userRoleItemView.hbs!";
import Radio from 'backbone.radio';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'li',
  ui:{
    'deleteButton': 'button[name="delete"]'
  },
  events:{
    'click @ui.deleteButton': 'delete'
  },
  delete(event){
    event.stopPropagation();
    this.model.destroy();
  }
});
