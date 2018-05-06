'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/image';
  },
  relations: [{
    type: BackboneRelational.HasOne,
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