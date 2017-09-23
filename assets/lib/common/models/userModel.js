'use strict';

import _ from 'underscore';
import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BackboneRelational from 'backbone.relational';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './departmentModel.js';
import './userRoleModel.js';
import './announcementModel.js';
import 'lib/inventory/models/inventoryAlertLogModel.js';

let Model = BaseUrlBaseModel.extend({
  initialize(attrs, options){
    if(this.id || attrs.id){
      let id = this.id?this.id:attrs.id;
      let myself = Radio.channel('data').request('myself');
      if(id == myself.id){
        this.listenTo(this, 'change', ()=>{
          myself.set(this.attributes);
        });
      }
    }
  },
  urlRoot(){
    return this.baseUrl+'/user';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'organization',
    relatedModel: 'OrganizationModel',
    includeInJSON: ['id']
  },{
    type: BackboneRelational.HasOne,
    key: 'defaultDepartment',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  },{
    type: BackboneRelational.HasOne,
    key: 'currentDepartment',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  },{
    type: BackboneRelational.HasOne,
    key: 'appAnnouncement',
    relatedModel: 'AnnouncementModel',
    includeInJSON: false
  },{
    type: BackboneRelational.HasMany,
    key: 'inventoryAlertLogs',
    relatedModel: 'InventoryAlertLogModel',
    includeInJSON: false
  }],
  defaults:{
    username: null,
    email: null,
    firstName: null,
    lastName: null,
    isActive: true,
    receivesInventoryAlert: false,
    organization: null,
    defaultDepartment: null,
    currentDepartment: null,
    userRoles: null,
    roleHierarchy: null,
    appAnnouncement: null,
    inventoryAlertLogs: null,
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
        if(this.get('roleHierarchy')){
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
        }else{
          return false;
        }
      });
    }
    return false;
  },
  isAccountOwner(){
    return this.get('isAccountOwner') || this.isGrantedRole('ROLE_DEV');
  }
});

globalNamespace.Models.UserModel = Model;

export default Model;