'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BackboneRelational from 'backbone.relational';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/inboundOrderModel.js';
import './binModel.js';
import './skuModel.js';
import './unitModel.js';
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
    unit: null,
    transform: null,
    reverseTransform: null,
  },
  triggerIsSelectedChangeOnRadio(){
    Radio.channel('inventory').trigger('change:isSelected:travelerId', this);
  },
  getUpdatadableAttributes(selectedCollection){
    let defaultAttributes = {
      inboundOrder: {
        title: 'Inbound Order',
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
      cost: {
        title: 'Cost',
        type: 'text'
      },
    };
    if(selectedCollection && selectedCollection instanceof Backbone.Collection){
      let unitType = null;
      let properties = null;
      let isUnitTypeSame = true;
      selectedCollection.each((tid)=>{
        if(unitType === null){
          unitType = tid.get('sku').get('unitType')?tid.get('sku').get('unitType'):false;
          if(unitType && tid.get('unit')){
            properties = tid.get('unit').get('properties');
          }
        }else{
          if(tid.get('sku').get('unitType') != unitType){
            isUnitTypeSame = false;
          }
        }
      });
      if(isUnitTypeSame){
        defaultAttributes.unit = {
          title: 'Unit',
          type: 'unit',
          properties: properties
        };
      }
    }
    return defaultAttributes;
  },
  getMassUpdateAttrs(){
    let attrs =  {
      id: this.get('id'),
      cid: this.cid,
      inboundOrder: {id: this.get('inboundOrder').get('id')},
      bin: {id: this.get('bin').get('id') },
      sku: {id: this.get('sku').get('id') },
      isVoid: this.get('isVoid'),
      quantity: this.get('quantity'),
      cost: this.get('cost'),
    };
    if(this.get('unit')){
      attrs.unit = this.get('unit').getMassUpdateAttrs();
    }
    return attrs;
  },
  getMassTransformAttrs(){
    let attrs =  this.getMassUpdateAttrs();
    if(this.get('transform')){
      attrs.transform = this.get('transform').getMassTransformAttrs();
    }
    return attrs;
  }
});

globalNamespace.Models.TravelerIdModel = Model;

export default Model;