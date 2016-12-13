'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/departmentModel.js';
import './partCategoryModel.js';
import './binTypeModel.js';
import './binSkuCountModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/bin';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'department',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'partCategory',
    relatedModel: 'PartCategoryModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'binType',
    relatedModel: 'BinTypeModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasMany,
    key: 'children',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'parent',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasMany,
    key: 'skuCount',
    relatedModel: 'BinSkuCountModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'bin',
      includeInJSON: ['id'],
    }
  }],
  defaults: {
    name: null,
    description: null,
    department: null,
    partCategory: null,
    binType: null,
    children: null,
    parent: null,
    isActive: null,
    skuCount: null,
    travelerIds: null,
    salesItems: null,
  },

});

globalNamespace.Models.BinModel = Model;

export default Model;