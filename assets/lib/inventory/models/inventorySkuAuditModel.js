'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './skuModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_sku_audit';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'sku',
    relatedModel: 'SkuModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    inventoryAudit: null,
    sku: null,
    userCount: null,
    systemCount: null,
  },

});

globalNamespace.Models.InventorySkuAuditModel = Model;

export default Model;