'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './salesItemModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_sales_item_movement';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'fromBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'toBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'salesItem',
    relatedModel: 'SalesItemModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    fromBin: null,
    toBin: null,
    movedAt: null,
    salesItem: null,
    tags: null,
  },

});

globalNamespace.Models.InventorySalesItemMovementModel = Model;

export default Model;