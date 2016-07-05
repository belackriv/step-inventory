'use strict';

import globalNamespace from 'lib/globalNamespace';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/office';
  },
  relations: [{
    type: Backbone.HasMany,
    key: 'departments',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id']
  }],
  defaults: {
    name: null,
    departments: null,
  }
});

globalNamespace.Models.OfficeModel = Model;

export default Model;