'use strict';

import globalNamespace from 'lib/globalNamespace';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/department';
  },
  relations: [{
    type: Backbone.HasMany,
    key: 'menuItems',
    relatedModel: 'MenuItemModel',
    includeInJSON: ['id']
  }],
  defaults: {
    name: null,
    office: null,
    menuItems: null,
  }
});

globalNamespace.Models.DepartmentModel = Model;

export default Model;