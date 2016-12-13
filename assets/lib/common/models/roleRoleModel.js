'use strict';


import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './roleModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/role_role';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'roleSource',
    relatedModel: 'RoleModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
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