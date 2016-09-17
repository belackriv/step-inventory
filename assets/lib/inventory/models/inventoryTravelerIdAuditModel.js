'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './travelerIdModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_tid_audit';
  },
  relations: [{
    type: Backbone.HasOne,
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