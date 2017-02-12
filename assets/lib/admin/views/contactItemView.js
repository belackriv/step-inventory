'use strict';

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './contactItemViewTpl.hbs!';

import AdminContactsEditView from './adminContactsEditView.js';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'li',
  ui:{
  	'editButton': 'button[data-ui="edit"]'
  },
  events:{
  	'click @ui.editButton': 'openEditContactDialog'
  },
  modelEvents:{
  	'change': 'render'
  },
  openEditContactDialog(event){
  	event.preventDefault();
    var options = {
      title: 'Edit Contact',
      width: '400px'
    };
    let view = new AdminContactsEditView({
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
