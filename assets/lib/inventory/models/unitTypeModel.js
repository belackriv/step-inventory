'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/uploadedImageModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/unit_type';
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