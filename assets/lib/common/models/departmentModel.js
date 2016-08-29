'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';

import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './officeModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/department';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'office',
    relatedModel: 'OfficeModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'departments',
      includeInJSON: ['id'],
    }
  }],
  defaults: {
    name: null,
    office: null,
    menuItems: null,
  },
  getTotalMenuItemCount(){
    let count = this.get('menuItems').length;
    this.get('menuItems').each((menuItem)=>{
      count += menuItem.getChildCount();
    });
    return count;
  }
});

globalNamespace.Models.DepartmentModel = Model;

export default Model;