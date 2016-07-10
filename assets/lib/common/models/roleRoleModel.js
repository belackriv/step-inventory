'use strict';


import globalNamespace from 'lib/globalNamespace';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/role_role';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'roleSource',
    relatedModel: 'RoleModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'roleTarget',
    relatedModel: 'RoleModel',
    includeInJSON: ['id'],
  }],
  defaults:{
    roleSource: null,
    roleTarget: null
  }
});

globalNamespace.Models.RoleModel = Model;

export default Model;