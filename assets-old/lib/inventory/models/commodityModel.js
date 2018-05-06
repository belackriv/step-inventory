'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/uploadedImageModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/commodity';
  },
  importData: {
    type: 'commodity',
    properties: [
      { name: 'name', required: true, description: null},
      { name: 'commodityId', required: false, description: null},
      { name: 'commodityAltId', required: false, description: null},
      { name: 'description', required: false, description: null},
      { name: 'isActive', required: true, description: 'Boolean'},
    ]
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'image',
    relatedModel: 'UploadedImageModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    name: null,
    commodityId: null,
    commodityAltId: null,
    description: null,
    image: null,
    isActive: null,
  }
});

globalNamespace.Models.CommodityModel = Model;

export default Model;