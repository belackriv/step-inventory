'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/uploadedImageModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/commodity';
  },
  relations: [{
    type: Backbone.HasOne,
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