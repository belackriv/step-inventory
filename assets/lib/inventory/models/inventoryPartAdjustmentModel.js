'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './partModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_part_adjustment';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'forBin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'part',
    relatedModel: 'PartModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    forBin: null,
    performedAt: null,
    part: null,
    oldCount: null,
    newCount: null,
  },

});

globalNamespace.Models.InventoryPartAdjustmentModel = Model;

export default Model;