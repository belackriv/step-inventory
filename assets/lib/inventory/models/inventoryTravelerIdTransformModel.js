'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './salesItemModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_tid_transform';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    transformedAt: null,
    fromTravelerIds: null,
    quantity: null,
    ratio: null,
    toTravelerIds: null,
    toSalesItems: null,
  },

});

globalNamespace.Models.InventoryTravelerIdTransformModel = Model;

export default Model;