'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './inventorySkuAuditModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_audit';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'forBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasMany,
    key: 'inventoryTravelerIdAudits',
    relatedModel: 'InventoryTravelerIdAuditModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'inventoryAudit',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasMany,
    key: 'inventorySalesItemAudits',
    relatedModel: 'InventorySalesItemAuditModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'inventoryAudit',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasMany,
    key: 'inventorySkuAudits',
    relatedModel: 'InventorySkuAuditModel',
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
    travelerIdCountDeviations: null,
    travelerIdMatchDeviations: null,
    salesItemCountDeviations: null,
    salesItemMatchDeviations: null,
    partCountDeviations: null,
    inventoryTravelerIdAudits: null,
    inventorySalesItemAudits: null,
    inventorySkuAudits: null,
  },

});

globalNamespace.Models.InventoryAuditModel = Model;

export default Model;