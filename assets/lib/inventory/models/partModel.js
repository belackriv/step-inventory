'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './partCategoryModel.js';
import './partGroupModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/part';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'partCategory',
    relatedModel: 'PartCategoryModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'partGroup',
    relatedModel: 'PartGroupModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    name: null,
    partId: null,
    partAltId: null,
    description: null,
    partCategory: null,
    partGroup: null,
    isActive: null
  }
});

globalNamespace.Models.PartModel = Model;

export default Model;