'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './travelerIdModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_tid_movement';
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
    key: 'travelerId',
    relatedModel: 'TravelerIdModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    fromBin: null,
    toBin: null,
    movedAt: null,
    travelerId: null,
    tags: null,
  },

});

globalNamespace.Models.InventoryTravelerIdMovementModel = Model;

export default Model;