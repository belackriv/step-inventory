'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './organizationModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/image';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'organization',
    relatedModel: 'OrganizationModel',
    includeInJSON: false,
    reverseRelation: false
  }],
  defaults: {
    organization: null,
    name: null,
    mimeType: null,
    width: null,
    height: null,
  },
});

globalNamespace.Models.UploadedImageModel = Model;

export default Model;