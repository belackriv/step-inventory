"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';
import viewTpl from  "./adminUsersEditView.hbs!";
import OfficeCollection from 'lib/common/models/officeCollection.js';
import RoleCollection from 'lib/common/models/roleCollection.js';
import UserRoleModel from 'lib/common/models/userRoleModel.js';
import FormChildListView from 'lib/common/views/formChildListView.js';
import userRoleItemViewTpl from './userRoleItemView.hbs!'


export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  regions:{
    'roles': '[data-ui-name="roles"]'
  },
  ui: {
    'usernameInput': 'input[name="username"]',
    'newPasswordInput': 'input[name="newPassword"]',
    'resetPasswordButton': 'button[name="resetPassword"]',
    'isActiveInput': 'input[name="isActive"]',
    'firstNameInput': 'input[name="firstName"]',
    'lastNameInput': 'input[name="lastName"]',
    'emailInput': 'input[name="email"]',
    'defaultDepartmentSelect': 'select[name="defaultDepartment"]',
    'roleSelect': 'select[name="role"]',
    'addRoleButton': 'button[name="addRole"]',
  },
  events: {
    'click @ui.resetPasswordButton': 'onResetPasswordButtonClicked',
    'click @ui.addRoleButton': 'onAddRoleButtonClicked'
  },
  onResetPasswordButtonClicked(event){
    event.preventDefault();
    this.ui.newPasswordInput.prop('disabled', false);
  },
  onAddRoleButtonClicked(event){
    event.preventDefault();
    if( this.model.get('selectedRole') && !this.model.hasUserRole(this.model.get('selectedRole')) ){
      let userRole = new UserRoleModel({
        user: this.model,
        role: this.model.get('selectedRole')
      });
    }
  },
  bindings: {
    '@ui.usernameInput': 'username',
    '@ui.newPasswordInput': 'newPassword',
    '@ui.isActiveInput': 'isActive',
    '@ui.firstNameInput': 'firstName',
    '@ui.lastNameInput': 'lastName',
    '@ui.emailInput': 'email',
    '@ui.defaultDepartmentSelect': {
      observe: 'defaultDepartment',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let departments = [];
          let collection = Radio.channel('data').request('collection', OfficeCollection);
          collection.each((office)=>{
            office.get('departments').each((department)=>{
                departments.push(department);
            });
          });
          return departments;
        },
      }
    },
    '@ui.roleSelect': {
      observe: 'selectedRole',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection() {
          let roles = Radio.channel('data').request('collection', RoleCollection);
          return roles;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
  onRender(){
    this.model.set('newPassword', null);
    this.showChildView('roles', new FormChildListView({
      collection: this.model.get('userRoles'),
      childTemplate: userRoleItemViewTpl
    }));
  }
});
