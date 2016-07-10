'use strict';

import Marionette from 'marionette';
import Radio from 'backbone.radio';

export default Marionette.View.extend({
  getTemplate(){
    return this.options.template;
  },
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
