'use strict';

import viewTpl from './inventorySalesItemAuditItemView.hbs!';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'tr',
  ui:{
    'removeButton': 'button[name="remove"]'
  },
  events:{
    'click @ui.removeButton': 'remove'
  },
  remove(event){
    event.preventDefault();
    if(this.ui.removeButton.data('confirm')){
      this.model.destroy();
    }else{
      this.ui.removeButton.text('Confirm?');
      this.ui.removeButton.data('confirm', true);
    }
  }
});
