'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/outboundOrderModel.js';
import './binModel.js';
import './skuModel.js';
import './unitModel.js';
import './inventoryTravelerIdTransformModel.js';

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
      key: 'salesItems',
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
      outboundOrder: {
        title: 'Outbound Order',
        type: 'select'
      },
      bin: {
        title: 'Bin',
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
  },
  getMassUpdateAttrs(){
    return {
      id: this.get('id'),
      outboundOrder: {id: this.get('outboundOrder').get('id')},
      bin: {id: this.get('bin').get('id') },
      isVoid: this.get('isVoid'),
      quantity: this.get('quantity'),
      revenue: this.get('revenue'),
    };
  }
});

globalNamespace.Models.SalesItemModel = Model;

export default Model;