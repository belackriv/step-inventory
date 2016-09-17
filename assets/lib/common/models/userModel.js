'use strict';


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
  relations: [{
    type: Backbone.HasOne,
    key: 'defaultDepartment',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  },{
    type: Backbone.HasOne,
    key: 'currentDepartment',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  },{
    type: Backbone.HasMany,
    key: 'userRoles',
    relatedModel: 'UserRoleModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'user',
      includeInJSON: ['id']
    }
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
    if(user.get('userRoles') && user.get('userRoles') instanceof Backbone.Collection){
      return user.get('userRoles').some((userRole)=>{
        if( userRole.get('role').get('role') == role){
          return true;
        }
        var roleLookup = subRole?subRole:userRole.get('role').get('role');
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
    }else{
      return false;
    }
  }
});

globalNamespace.Models.UserModel = Model;

export default Model;