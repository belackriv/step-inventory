'use strict';

import jquery from 'jquery';
import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/inventory/models/salesItemModel.js';
import './customerModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/outbound_order';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'customer',
    relatedModel: 'CustomerModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    label: null,
    customer: null,
    description: null,
    isVoid: false,
    isShipped: false,
    salesItems: null,
    //virtual, since salesItems starts empty
    salesItemCount: null,
  },
  ship(){
    let thisModel = this;
    return new Promise((resolve, reject)=>{
      jquery.ajax(thisModel.url()+'/ship',{
        accepts: {
          json: 'application/json'
        },
        dataType: 'json'
      }).done((data)=>{
        thisModel.set(data);
        resolve(thisModel);
      }).fail((response)=>{
        reject(response);
      });
    });
  }
});

globalNamespace.Models.OutboundOrderModel = Model;

export default Model;