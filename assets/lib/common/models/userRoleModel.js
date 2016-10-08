'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './roleModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/user_role';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'role',
    relatedModel: 'RoleModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'user',
    relatedModel: 'UserModel',
    includeInJSON: false,
    reverseRelation:{
      key: 'userRoles',
      includeInJSON: true
    }
  }],
  defaults:{
    user: null,
    role: null
  }
});

globalNamespace.Models.UserRoleModel = Model;

export default Model;