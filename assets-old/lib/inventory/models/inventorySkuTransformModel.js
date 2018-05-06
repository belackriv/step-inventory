'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binSkuCountModel.js';
import './salesItemModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_sku_transform';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'fromBinSkuCount',
    relatedModel: 'BinSkuCountModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'toSalesItem',
    relatedModel: 'SalesItemModel',
    includeInJSON: true,
  }],
  defaults: {
    byUser: null,
    transformedAt: null,
    fromBinSkuCount: null,
    quantity: null,
    toSalesItem: null,
    isVoid: null,
  },

});

globalNamespace.Models.InventorySkuTransformModel = Model;

export default Model;