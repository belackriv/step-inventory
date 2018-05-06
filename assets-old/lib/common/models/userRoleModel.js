'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './roleModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/user_role';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'role',
    relatedModel: 'RoleModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
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