'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './binModel.js';
import './travelerIdModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_tid_movement';
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