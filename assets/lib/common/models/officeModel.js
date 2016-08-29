'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/office';
  },
  defaults: {
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