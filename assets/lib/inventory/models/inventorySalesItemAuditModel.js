'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './salesItemModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_sales_item_audit';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'salesItem',
    relatedModel: 'SalesItemModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    inventoryAudit: null,
    salesItemLabel: null,
    salesItem: null,
  },

});

globalNamespace.Models.InventorySalesItemAuditModel = Model;

export default Model;