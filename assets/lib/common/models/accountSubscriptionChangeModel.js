'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import AccountChangeModel from './accountChangeModel.js';

let Model = AccountChangeModel.extend({
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'oldSubscription',
    relatedModel: 'SubscriptionModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'newSubscription',
    relatedModel: 'SubscriptionModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    oldSubscription: null,
    newSubscription: null,
  },
});

globalNamespace.Models.AccountSubscriptionChangeModel = Model;

export default Model;