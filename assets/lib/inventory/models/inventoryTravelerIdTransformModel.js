'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userModel.js';
import './salesItemModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_tid_transform';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'byUser',
    relatedModel: 'UserModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    byUser: null,
    transformedAt: null,
    quantity: null,
    ratio: null,
    fromTravelerIds: null,
    toTravelerIds: null,
    toSalesItems: null,
  },
  getMassTransformAttrs(){
    let attrs =  {
      id: this.get('id'),
      cid: this.cid,
      //skip byUser, it is set on server
      quantity: this.get('quantity'),
      ratio: this.get('ratio'),
      fromTravelerIds: [],
      toTravelerIds: [],
      toSalesItems: [],
    };
    this.get('fromTravelerIds').each((travelerId)=>{
      attrs.fromTravelerIds.push({id: travelerId.get('id')});
    });
    this.get('toTravelerIds').each((travelerId)=>{
      attrs.toTravelerIds.push(travelerId.getMassTransformAttrs());
    });
    this.get('toSalesItems').each((salesItem)=>{
      attrs.toSalesItems.push(salesItem.getMassTransformAttrs());
    });
    return attrs;
  }
});

globalNamespace.Models.InventoryTravelerIdTransformModel = Model;

export default Model;