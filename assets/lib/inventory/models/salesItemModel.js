'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/outboundOrderModel.js';
import './binModel.js';
import './skuModel.js';

let Model = BaseUrlBaseModel.extend({
  initialize(){
    this.listenTo(this, 'change:isSelected', this.triggerIsSelectedChangeOnRadio);
  },
  urlRoot(){
    return this.baseUrl+'/sales_item';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'outboundOrder',
    relatedModel: 'OutboundOrderModel',
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
      key: 'salesItems',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'sku',
    relatedModel: 'SkuModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'reverseTransform',
    relatedModel: 'InventoryTravelerIdTransformModel',
    includeInJSON: false,
    reverseRelation: {
      key: 'toSalesItems',
      includeInJSON: true,
    }
  }],
  defaults: {
    outboundOrder: null,
    label: null,
    bin: null,
    sku: null,
    isVoid: null,
    quantity: null,
    revenue: null,
    reverseTransform: null,
  },
  triggerIsSelectedChangeOnRadio(){
    Radio.channel('inventory').trigger('change:isSelected:salesItem', this);
  },
  getUpdatadableAttributes(){
    return {
      inboundOrder: {
        title: 'Inbound Order',
        type: 'select'
      },
      label: {
        title: 'Label',
        type: 'text'
      },
      bin: {
        title: 'Bin',
        type: 'select'
      },
      sku: {
        title: 'SKU',
        type: 'select'
      },
      isVoid: {
        title: 'Is Void? (Use "Yes" and "No" for multiple)',
        type: 'checkbox'
      },
      quantity: {
        title: 'Quantity',
        type: 'text'
      },
      revenue: {
        title: 'Revenue',
        type: 'text'
      },
    };
  }
});

globalNamespace.Models.SalesItemModel = Model;

export default Model;