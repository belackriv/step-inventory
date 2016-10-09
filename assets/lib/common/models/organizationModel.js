'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './uploadedImageModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/organization';
  },
   relations: [{
    type: Backbone.HasOne,
    key: 'logo',
    relatedModel: 'UploadedImageModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    name: null,
    logo: null,
  },
});

globalNamespace.Models.OrganizationModel = Model;

export default Model;