'use strict';

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './addressItemViewTpl.hbs!';

import AdminAddressesEditView from './adminAddressesEditView.js';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'li',
  ui:{
  	'editButton': 'button[data-ui="edit"]'
  },
  events:{
  	'click @ui.editButton': 'openEditAddressDialog'
  },
  modelEvents:{
  	'change': 'render'
  },
  openEditAddressDialog(event){
  	event.preventDefault();
    var options = {
      title: 'Edit Address',
      width: '400px'
    };
    let view = new AdminAddressesEditView({
      model: this.model,
      postDelete(){
        Radio.channel('dialog').trigger('close');
      }
    });
    this.listenTo(view, 'show:list', ()=>{
      Radio.channel('dialog').trigger('close');
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  }
});
