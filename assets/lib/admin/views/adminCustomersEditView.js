"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminCustomersEditView.hbs!";

import ContactModel from 'lib/common/models/contactModel.js';
import AdminContactsEditView from './adminContactsEditView.js';
import ContactListView from './contactListView.js';

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  regions: {
    'contacts': '[data-ui-name="contacts"]'
  },
  ui: {
    'nameInput': 'input[name="name"]',
    'addContactButton': 'button[data-ui-name="addContact"]'
  },
  events: {
    'click @ui.addContactButton': 'openAddContactDialog'
  },
  bindings: {
    '@ui.nameInput': 'name',
  },
  onRender(){
    this.showChildView('contacts', new ContactListView({
      collection: this.model.get('contacts'),
    }));
  },
  openAddContactDialog(event){
    event.preventDefault();
    var options = {
      title: 'Add Contact',
      width: '400px'
    };
    let contact = ContactModel.findOrCreate({
      customer: this.model
    });
    let view = new AdminContactsEditView({
      model: contact,
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
