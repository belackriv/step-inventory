'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/inboundOrderModel.js';
import './binModel.js';
import './skuModel.js';
import './inventoryTravelerIdTransformModel.js';

let Model = BaseUrlBaseModel.extend({
  modelName: 'TravelerId',
  initialize(){
    this.listenTo(this, 'change:isSelected', this.triggerIsSelectedChangeOnRadio);
  },
  urlRoot(){
    return this.baseUrl+'/tid';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'inboundOrder',
    relatedModel: 'InboundOrderModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'travelerIds',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'bin',
    relatedModel: 'BinModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'travelerIds',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'sku',
    relatedModel: 'SkuModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'transform',
    relatedModel: 'InventoryTravelerIdTransformModel',
    includeInJSON: true,
    reverseRelation: {
      key: 'fromTravelerIds',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'reverseTransform',
    relatedModel: 'InventoryTravelerIdTransformModel',
    includeInJSON: false,
    reverseRelation: {
      key: 'toTravelerIds',
      includeInJSON: true,
    }
  }],
  defaults: {
    inboundOrder: null,
    label: null,
    bin: null,
    sku: null,
    isVoid: null,
    quantity: null,
    cost: null,
    transform: null,
    reverseTransform: null,
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
      /*
      label: {
        title: 'Label',
        type: 'text'
      },
      */
      bin: {
        title: 'Bin',
        type: 'select'
      },
      /*
      sku: {
        title: 'SKU',
        type: 'select'
      },
      */
      isVoid: {
        title: 'Is Void? (Use "Yes" and "No" for multiple)',
        type: 'checkbox'
      },
      quantity: {
        title: 'Quantity',
        type: 'text'
      },
      cost: {
        title: 'Cost',
        type: 'text'
      },
    };
  }
});

globalNamespace.Models.TravelerIdModel = Model;

export default Model;