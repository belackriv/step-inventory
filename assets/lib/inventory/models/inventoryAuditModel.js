'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './inventoryPartAuditModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_audit';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'forBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasMany,
    key: 'inventoryPartAudits',
    relatedModel: 'InventoryPartAuditModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'inventoryAudit',
      includeInJSON: ['id'],
    }
  }],
  defaults: {
    byUser: null,
    forBin: null,
    startedAt: null,
    endedAt: null,
    totalDeviations: null,
    serialCountDeviations: null,
    serialMatchDeviations: null,
    partCountDeviations: null,
    inventoryPartAudits: null,
  },

});

globalNamespace.Models.InventoryAuditModel = Model;

export default Model;