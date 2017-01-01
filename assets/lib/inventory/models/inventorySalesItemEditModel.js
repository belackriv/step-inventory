'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './salesItemModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_sales_item_edit';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'salesItem',
    relatedModel: 'SalesItemModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    oldAttributes: null,
    newAttributes: null,
    editedAt: null,
    salesItem: null,
  },

});

globalNamespace.Models.InventorySalesItemEditModel = Model;

export default Model;