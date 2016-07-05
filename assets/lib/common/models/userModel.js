'use strict';


import globalNamespace from 'lib/globalNamespace';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/user';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'defaultDepartment',
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
    roles: null,
  }
});

globalNamespace.Models.UserModel = Model;

export default Model;