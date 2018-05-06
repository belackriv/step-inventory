'use strict';

import _ from 'underscore';
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
    if(!this.options.noDelete){
      event.stopPropagation();
      this.model.destroy();
    }
  }
});
