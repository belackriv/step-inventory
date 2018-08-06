'use strict';

import jquery from 'jquery';
import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/inventory/models/travelerIdModel.js';
import './clientModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inbound_order';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'client',
    relatedModel: 'ClientModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    label: null,
    client: null,
    description: null,
    isVoid: false,
    expectedAt: null,
    isReceived: false,
    receivedAt: null,
    travelerIds: null,
    //virtual, since travelerIds starts empty
    travelerIdCount: null,
  },
  receive(){
    let thisModel = this;
    return new Promise((resolve, reject)=>{
      jquery.ajax(thisModel.url()+'/receive',{
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

globalNamespace.Models.InboundOrderModel = Model;

export default Model;