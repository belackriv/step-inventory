'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './partModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_part_audit';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'part',
    relatedModel: 'PartModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    inventoryAudit: null,
    part: null,
    userCount: null,
    systemCount: null,
  },

});

globalNamespace.Models.InventoryPartAuditModel = Model;

export default Model;