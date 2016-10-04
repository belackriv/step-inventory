'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './organizationModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/organization';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'organization',
    relatedModel: 'OrganizationModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'offices',
      includeInJSON: false,
    }
  }],
  defaults: {
    organization: null,
    name: null,
    departments: null,
  },
  getDepartmentTotalMenuItemCount(departmentId){
  	let department = this.get('departments').get(departmentId);
  	let count = department.getTotalMenuItemCount();
  	return count;
  }
});

globalNamespace.Models.OfficeModel = Model;

export default Model;