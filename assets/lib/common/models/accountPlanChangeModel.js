'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import AccountChangeModel from './accountChangeModel.js';

let Model = AccountChangeModel.extend({
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'oldPlan',
    relatedModel: 'PlanModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'newPlan',
    relatedModel: 'PlanModel',
    includeInJSON: ['id', 'plan'],
    reverseRelation: false
  }],
  defaults: {
    oldPlan: null,
    newPlan: null,
  },
});

globalNamespace.Models.AccountPlanChangeModel = Model;

export default Model;