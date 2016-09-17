'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/inboundOrderModel';
import 'lib/accounting/models/outboundOrderModel';

let Model = BaseUrlBaseModel.extend({
  initialize(){
    this.listenTo(this, 'change:isSelected', this.triggerIsSelectedChangeOnRadio);
  },
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
    reverseRelation: {
      key: 'travelerIds',
      includeInJSON: ['id'],
    }
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
    cost: null,
    revenue: null,
  },
  triggerIsSelectedChangeOnRadio(){
    Radio.channel('inventory').trigger('change:isSelected:travelerId', this);
  },
  getUpdatadableAttributes(){
    return {
      inboundOrder: {
        title: 'Inbound Order',
        type: 'select'
      },
      outboundOrder: {
        title: 'Outbound Order',
        type: 'select'
      },
      label: {
        title: 'Label',
        type: 'text'
      },
      serial: {
        title: 'Serial',
        type: 'text'
      },
      bin: {
        title: 'Bin',
        type: 'select'
      },
      part: {
        title: 'Part',
        type: 'select'
      },
      isVoid: {
        title: 'Is Void? (Use "Yes" and "No" for multiple)',
        type: 'checkbox'
      },
      cost: {
        title: 'Cost',
        type: 'text'
      },
      revenue: {
        title: 'Revenue',
        type: 'text'
      },
    };
  }
});

globalNamespace.Models.TravelerIdModel = Model;

export default Model;