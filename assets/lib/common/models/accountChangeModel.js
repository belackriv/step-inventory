'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/account_change';
  },
  subModelTypeAttribute: 'discriminator',
  subModelTypes: {
    'AccountOwnerChange': 'AccountOwnerChangeModel',
    'AccountPlanChange': 'AccountPlanChangeModel'
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'changedBy',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },/*{
    type: BackboneRelational.HasOne,
    key: 'account',
    relatedModel: 'AccountModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }*/],
  defaults: {
    changedBy: null,
    changedAt: null,
    account: null,
  },
});

globalNamespace.Models.AccountChangeModel = Model;

export default Model;
