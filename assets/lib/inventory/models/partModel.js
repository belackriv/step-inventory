'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './partCategoryModel.js';
import './partGroupModel.js';
import 'lib/common/models/uploadedImageModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/part';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'partCategory',
    relatedModel: 'PartCategoryModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'partGroup',
    relatedModel: 'PartGroupModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'image',
    relatedModel: 'UploadedImageModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    name: null,
    partId: null,
    partAltId: null,
    description: null,
    partCategory: null,
    partGroup: null,
    isActive: null,
    image: null
  }
});

globalNamespace.Models.PartModel = Model;

export default Model;