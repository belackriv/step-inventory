"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminClientsEditView.hbs!";

import ContactModel from 'lib/common/models/contactModel.js';
import AdminContactsEditView from './adminContactsEditView.js';
import ContactListView from './contactListView.js';

import AddressModel from 'lib/common/models/addressModel.js';
import AdminAddressesEditView from './adminAddressesEditView.js';
import AddressListView from './addressListView.js';

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  regions: {
    'contacts': '[data-ui-name="contacts"]',
    'addresses': '[data-ui-name="addresses"]',
  },
  ui: {
    'nameInput': 'input[name="name"]',
    'addContactButton': 'button[data-ui-name="addContact"]',
    'addAddressButton': 'button[data-ui-name="addAddress"]'
  },
  events: {
    'click @ui.addContactButton': 'openAddContactDialog',
    'click @ui.addAddressButton': 'openAddAddressDialog'
  },
  bindings: {
    '@ui.nameInput': 'name',
  },
  onRender(){
    this.showChildView('contacts', new ContactListView({
      collection: this.model.get('contacts'),
    }));
    this.showChildView('addresses', new AddressListView({
      collection: this.model.get('addresses'),
    }));
  },
  openAddContactDialog(event){
    event.preventDefault();
    var options = {
      title: 'Add Contact',
      width: '400px'
    };
    let contact = ContactModel.findOrCreate({
      client: this.model
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
  },
  openAddAddressDialog(event){
    event.preventDefault();
    var options = {
      title: 'Add Address',
      width: '400px'
    };
    let address = AddressModel.findOrCreate({
      client: this.model
    });
    let view = new AdminAddressesEditView({
      model: address,
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
