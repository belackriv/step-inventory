'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/uploadedImageModel.js';
import './unitTypePropertyModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/unit_type';
  },
  importData: {
    type: 'unitType',
    properties: [
      { name: 'name', required: true, description: null},
      { name: 'manufacturer', required: false, description: null},
      { name: 'model', required: false, description: null},
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
    manufacturer: null,
    model: null,
    description: null,
    image: null,
    isActive: null,
  }
});

globalNamespace.Models.UnitTypeModel = Model;

export default Model;