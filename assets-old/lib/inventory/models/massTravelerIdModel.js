'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/inboundOrderModel';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/mass_tid';
  },
  relations: [{
    type: BackboneRelational.HasMany,
    key: 'travelerIds',
    relatedModel: 'TravelerIdModel',
    includeInJSON: true,
  }],
  defaults: {
    travelerIds: null,
    //only for client for now
    serials: null,
    serialsArray: null,
    count: null,
    type: null,
  },
  toJSON(){
    let attrs = {
      id: this.get('id'),
      type: this.get('type'),
      serials: this.get('serials'),
      serialsArray: this.get('serialsArray'),
      count: this.get('count'),
      travelerIds: []
    };
    this.get('travelerIds').each((travelerId)=>{
      if(attrs.type === 'transform'){
        attrs.travelerIds.push(travelerId.getMassTransformAttrs());
      }else{
        attrs.travelerIds.push(travelerId.getMassUpdateAttrs());
      }
    });
    return attrs;
  }
});

globalNamespace.Models.MassTravelerIdModel = Model;

export default Model;