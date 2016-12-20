'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/payment_source';
  },
  subModelTypeAttribute: 'discriminator',
  subModelTypes: {
    'PaymentCardSource': 'PaymentCardSourceModel'
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'account',
    relatedModel: 'AccountModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    externalId: null,
    account: null,
  },
});

globalNamespace.Models.PaymentSourceModel = Model;

export default Model;
