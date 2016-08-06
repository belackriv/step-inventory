'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './partCategoryModel.js';
import './binTypeModel.js';
import './binPartCountModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/bin';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'partCategory',
    relatedModel: 'PartCategoryModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'binType',
    relatedModel: 'BinTypeModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasMany,
    key: 'children',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'parent',
      includeInJSON: ['id'],
    }
  },{
    type: Backbone.HasMany,
    key: 'partCount',
    relatedModel: 'BinPartCountModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'bin',
      includeInJSON: ['id'],
    }
  }],
  defaults: {
    name: null,
    description: null,
    partCategory: null,
    binType: null,
    children: null,
    parent: null,
    isActive: null,
    partCount: null,
  },

});

globalNamespace.Models.BinModel = Model;

export default Model;