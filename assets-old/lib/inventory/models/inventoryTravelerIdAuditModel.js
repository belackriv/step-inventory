'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './travelerIdModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_tid_audit';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'travelerId',
    relatedModel: 'TravelerIdModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    inventoryAudit: null,
    travelerIdLabel: null,
    travelerId: null,
  },

});

globalNamespace.Models.InventoryTravelerIdAuditModel = Model;

export default Model;