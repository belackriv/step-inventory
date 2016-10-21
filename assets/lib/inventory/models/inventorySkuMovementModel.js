'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './skuModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_sku_movement';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'fromBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'toBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'sku',
    relatedModel: 'SkuModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    fromBin: null,
    toBin: null,
    movedAt: null,
    sku: null,
    count: null,
    tags: null,
  },

});

globalNamespace.Models.InventorySkuMovementModel = Model;

export default Model;