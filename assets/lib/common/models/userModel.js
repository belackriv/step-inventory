'use strict';

import _ from 'underscore';
import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './departmentModel.js';
import './userRoleModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/user';
  },
  subModelTypes: {
    'myself': 'MyselfModel',
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'organization',
    relatedModel: 'OrganizationModel',
    includeInJSON: ['id']
  },{
    type: Backbone.HasOne,
    key: 'defaultDepartment',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  },{
    type: Backbone.HasOne,
    key: 'currentDepartment',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  }],
  defaults:{
    username: null,
    email: null,
    firstName: null,
    lastName: null,
    isActive: null,
    defaultDepartment: null,
    currentDepartment: null,
    userRoles: null,
    roleHierarchy: null,
  },
  hasUserRole(userRole){
    return this.get('userRoles').get(userRole)?true:false;
  },
  isGrantedRole(role, userAccount, subRole){
    var user = userAccount?userAccount:this;
    if(user.get('userRoles')){
      let userRoles = [];
      if(user.get('userRoles') instanceof Backbone.Collection){
        userRoles = user.get('userRoles').models;
      }else{
        userRoles = user.get('userRoles');
      }
      return _.some(userRoles, (userRole)=>{
        let userRoleStr = null;
        if(userRole instanceof Backbone.Model){
          if(userRole.get('role') instanceof Backbone.Model){
            userRoleStr = userRole.get('role').get('role');
          }else{
            userRoleStr = userRole.get('role').role;
          }
        }else{
          if(userRole && userRole.role){
            userRoleStr = userRole.role.role;
          }
        }
        if(userRoleStr == role){
          return true;
        }
        var roleLookup = subRole?subRole:userRoleStr;
        var userGrantedRoles = this.get('roleHierarchy')[roleLookup];
        if(userGrantedRoles){
          if(userGrantedRoles.indexOf(role) > -1){
            return true;
          }else{
            for(let subRole of userGrantedRoles){
              if(this.isGrantedRole(role, user, subRole)){
                return true;
              }
            }
          }
        }
      });
    }
    return false;
  }
});

globalNamespace.Models.UserModel = Model;

export default Model;