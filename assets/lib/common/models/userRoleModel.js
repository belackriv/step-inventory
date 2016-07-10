'use strict';

import globalNamespace from 'lib/globalNamespace';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/user_role';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'user',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'userRoles',
      includeInJSON: true
    }
  },{
    type: Backbone.HasOne,
    key: 'role',
    relatedModel: 'RoleModel',
    includeInJSON: ['id']
  }],
  defaults:{
    user: null,
    role: null
  }
});

globalNamespace.Models.UserRoleModel = Model;

export default Model;