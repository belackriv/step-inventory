'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './organizationModel.js';
import './userModel.js';
import './subscriptionModel.js';
import './accountChangeModel.js';
import './billModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/account';
  },
   relations: [{
    type: BackboneRelational.HasOne,
    key: 'organization',
    relatedModel: 'OrganizationModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'owner',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'subscription',
    relatedModel: 'SubscriptionModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasMany,
    key: 'accountChanges',
    relatedModel: 'AccountChangeModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasMany,
    key: 'bills',
    relatedModel: 'BillModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    organization: null,
    owner: null,
    subscription: null,
    accountChanges: null,
    bills: null,
  },
});

globalNamespace.Models.AccountModel = Model;

export default Model;