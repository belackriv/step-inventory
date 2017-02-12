'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/contactModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/client';
  },
  relations: [{
    type: BackboneRelational.HasMany,
    key: 'contacts',
    relatedModel: 'ContactModel',
    includeInJSON: false,
    reverseRelation: {
      key: 'client',
      includeInJSON: ['id'],
    }
  }],
  defaults: {
    name: null,
    contacts: null,
  },
});

globalNamespace.Models.ClientModel = Model;

export default Model;