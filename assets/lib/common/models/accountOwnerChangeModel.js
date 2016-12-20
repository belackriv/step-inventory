'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import AccountChangeModel from './accountChangeModel.js';

let Model = AccountChangeModel.extend({
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'oldOwner',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'newOwner',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    oldOwner: null,
    newOwner: null,
  },
});

globalNamespace.Models.AccountOwnerChangeModel = Model;

export default Model;