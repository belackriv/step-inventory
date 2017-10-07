'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './organizationModel.js';
import './userModel.js';
import './subscriptionModel.js';
import './paymentSourceModel.js';
import './accountChangeModel.js';
import './accountOwnerChangeModel.js';
import './accountPlanChangeModel.js';
import './accountAutoPlanChangeModel.js';
import './billModel.js';
import './currentSessionModel.js';

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
    type: BackboneRelational.HasMany,
    key: 'ownerSelections',
    relatedModel: 'UserModel',
    includeInJSON: false,
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'subscription',
    relatedModel: 'SubscriptionModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasMany,
    key: 'paymentSources',
    relatedModel: 'PaymentSourceModel',
    includeInJSON:  false,
    reverseRelation: {
      key: 'account',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasMany,
    key: 'currentSessions',
    relatedModel: 'CurrentSessionModel',
    includeInJSON: false,
    reverseRelation: {
      key: 'account',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasMany,
    key: 'accountChanges',
    relatedModel: 'AccountChangeModel',
    includeInJSON: false,
    reverseRelation: {
      key: 'account',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasMany,
    key: 'bills',
    relatedModel: 'BillModel',
    includeInJSON:  false,
    reverseRelation: {
      key: 'account',
      includeInJSON: ['id'],
    }
  }],
  defaults: {
    organization: null,
    owner: null,
    subscription: null,
    currentSessions: null,
    accountChanges: null,
    bills: null,
  }
});

globalNamespace.Models.AccountModel = Model;

export default Model;