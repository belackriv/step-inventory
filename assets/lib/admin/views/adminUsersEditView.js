"use strict";

import _ from 'underscore';
import Radio from 'backbone.radio';
import Marionette from 'marionette';
import viewTpl from  "./adminUsersEditView.hbs!";
import OfficeCollection from 'lib/common/models/officeCollection.js';

export default Marionette.View.extend({
  template: viewTpl,
   behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  ui: {
    'usernameInput': 'input[name="username"]',
    'isActiveInput': 'input[name="isActive"]',
    'firstNameInput': 'input[name="firstName"]',
    'lastNameInput': 'input[name="lastName"]',
    'emailInput': 'input[name="email"]',
    'defaultDepartmentSelect': 'select[name="defaultDepartment"]',
  },
  bindings: {
    '@ui.usernameInput': 'username',
    '@ui.isActiveInput': 'isActive',
    '@ui.firstNameInput': 'firstName',
    '@ui.lastNameInput': 'lastName',
    '@ui.emailInput': 'email',
    '@ui.defaultDepartmentSelect': {
      observe: 'defaultDepartment',
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let departments = [];
          let collection = Radio.channel('data').request('collection', OfficeCollection);
          collection.each((office)=>{
            departments = departments.concat(office.get('departments').models);
          });
          return departments;
        },
      }
    },
  },
});
