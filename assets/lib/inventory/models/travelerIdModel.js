'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/inboundOrderModel';
import 'lib/accounting/models/outboundOrderModel';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/tid';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'inboundOrder',
    relatedModel: 'InboundOrderModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'travelerIds',
      includeInJSON: ['id'],
    }
  },{
    type: Backbone.HasOne,
    key: 'outboundOrder',
    relatedModel: 'OutboundOrderModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'travelerIds',
      includeInJSON: ['id'],
    }
  },{
    type: Backbone.HasOne,
    key: 'bin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasOne,
    key: 'part',
    relatedModel: 'PartModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    inboundOrder: null,
    outboundOrder: null,
    label: null,
    serial: null,
    bin: null,
    part: null,
    isVoid: null,
  },
});

globalNamespace.Models.TravelerIdModel = Model;

export default Model;