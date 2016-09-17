'use strict';


import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/role';
  },
  defaults:{
    isAllowedToSwitch: null,
    name: null,
    role: null,
  }
});

globalNamespace.Models.RoleModel = Model;

export default Model;